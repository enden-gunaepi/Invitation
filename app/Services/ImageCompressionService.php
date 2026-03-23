<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageCompressionService
{
    private const TARGET_MAX_BYTES = 1048576; // 1 MB
    private const MAX_DIMENSION = 2200;
    private const WEBP_QUALITIES = [82, 74, 66, 58, 50];

    /**
     * Compress an uploaded image to WebP (best effort <= 1 MB), then store in public disk.
     */
    public function compressAndStore(UploadedFile $file, string $directory): string
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagewebp')) {
            throw new \RuntimeException('GD with WebP support is required for image compression.');
        }

        $this->ensureSafeToDecode($file);
        $this->ensureMemoryHeadroom();
        $sourceImage = $this->createSourceImage($file);
        if ($sourceImage === false) {
            throw new \RuntimeException('Failed to decode uploaded image.');
        }

        $sourceImage = $this->autoOrient($sourceImage, $file);
        $resized = $this->resizeToMaxDimension($sourceImage, self::MAX_DIMENSION);

        imagedestroy($sourceImage);

        $bestTmpPath = null;
        $bestSize = null;
        foreach (self::WEBP_QUALITIES as $quality) {
            $encodedFile = $this->encodeWebpToTempFile($resized, $quality);
            if ($encodedFile === null) {
                continue;
            }

            $bestTmpPath = $encodedFile;
            $bestSize = @filesize($encodedFile) ?: null;
            if ($bestSize !== null && $bestSize <= self::TARGET_MAX_BYTES) {
                break;
            }
        }

        imagedestroy($resized);

        if ($bestTmpPath === null) {
            throw new \RuntimeException('Failed to encode compressed WebP image.');
        }

        $path = trim($directory, '/') . '/' . Str::uuid() . '.webp';
        $stream = fopen($bestTmpPath, 'rb');
        if ($stream === false) {
            @unlink($bestTmpPath);
            throw new \RuntimeException('Failed to open compressed temp file.');
        }

        Storage::disk('public')->put($path, $stream);
        fclose($stream);
        @unlink($bestTmpPath);

        return $path;
    }

    private function createSourceImage(UploadedFile $file): \GdImage|false
    {
        $path = $file->getRealPath();
        if ($path === false) {
            return false;
        }

        $mimeType = strtolower((string) $file->getMimeType());
        $decoded = match ($mimeType) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/webp' => @imagecreatefromwebp($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/bmp', 'image/x-ms-bmp' => function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : false,
            default => false,
        };

        if ($decoded instanceof \GdImage) {
            return $decoded;
        }

        $binary = @file_get_contents($path);
        if ($binary === false) {
            return false;
        }

        return @imagecreatefromstring($binary);
    }

    private function ensureSafeToDecode(UploadedFile $file): void
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new \RuntimeException('Uploaded image path is invalid.');
        }

        $info = @getimagesize($path);
        if ($info === false) {
            throw new \RuntimeException('Failed to read image metadata.');
        }

        $width = (int) ($info[0] ?? 0);
        $height = (int) ($info[1] ?? 0);
        if ($width <= 0 || $height <= 0) {
            throw new \RuntimeException('Invalid image dimension.');
        }
    }

    private function ensureMemoryHeadroom(): void
    {
        $currentLimit = (string) ini_get('memory_limit');
        $currentBytes = $this->toBytes($currentLimit);
        $targetBytes = 512 * 1024 * 1024; // 512 MB

        if ($currentBytes > 0 && $currentBytes < $targetBytes) {
            @ini_set('memory_limit', '512M');
        }
    }

    private function autoOrient(\GdImage $image, UploadedFile $file): \GdImage
    {
        $mimeType = strtolower((string) $file->getMimeType());
        if (!in_array($mimeType, ['image/jpeg', 'image/jpg'], true)) {
            return $image;
        }

        $orientation = 1;
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($file->getRealPath());
            $orientation = (int) ($exif['Orientation'] ?? 1);
        } else {
            $orientation = $this->readJpegOrientation($file->getRealPath()) ?? 1;
        }

        return match ($orientation) {
            3 => imagerotate($image, 180, 0) ?: $image,
            6 => imagerotate($image, -90, 0) ?: $image,
            8 => imagerotate($image, 90, 0) ?: $image,
            default => $image,
        };
    }

    private function readJpegOrientation(string|false $path): ?int
    {
        if ($path === false || !is_file($path)) {
            return null;
        }

        $binary = @file_get_contents($path);
        if ($binary === false || strlen($binary) < 4) {
            return null;
        }

        if (substr($binary, 0, 2) !== "\xFF\xD8") {
            return null;
        }

        $offset = 2;
        $length = strlen($binary);
        while ($offset + 4 <= $length) {
            if (ord($binary[$offset]) !== 0xFF) {
                break;
            }

            $marker = ord($binary[$offset + 1]);
            $offset += 2;

            if ($marker === 0xDA || $marker === 0xD9) { // SOS / EOI
                break;
            }

            if ($offset + 2 > $length) {
                break;
            }

            $segmentLength = unpack('n', substr($binary, $offset, 2))[1] ?? 0;
            if ($segmentLength < 2 || $offset + $segmentLength > $length) {
                break;
            }

            if ($marker === 0xE1) { // APP1 (Exif)
                $segment = substr($binary, $offset + 2, $segmentLength - 2);
                $orientation = $this->readExifOrientationFromSegment($segment);
                if ($orientation !== null) {
                    return $orientation;
                }
            }

            $offset += $segmentLength;
        }

        return null;
    }

    private function readExifOrientationFromSegment(string $segment): ?int
    {
        if (!str_starts_with($segment, "Exif\0\0")) {
            return null;
        }

        $tiff = substr($segment, 6);
        if (strlen($tiff) < 8) {
            return null;
        }

        $byteOrder = substr($tiff, 0, 2);
        $littleEndian = match ($byteOrder) {
            'II' => true,
            'MM' => false,
            default => null,
        };
        if ($littleEndian === null) {
            return null;
        }

        $ifdOffset = $this->readUInt32(substr($tiff, 4, 4), $littleEndian);
        if ($ifdOffset === null || $ifdOffset + 2 > strlen($tiff)) {
            return null;
        }

        $entryCount = $this->readUInt16(substr($tiff, $ifdOffset, 2), $littleEndian);
        if ($entryCount === null) {
            return null;
        }

        $entryBase = $ifdOffset + 2;
        for ($i = 0; $i < $entryCount; $i++) {
            $entryOffset = $entryBase + ($i * 12);
            if ($entryOffset + 12 > strlen($tiff)) {
                break;
            }

            $entry = substr($tiff, $entryOffset, 12);
            $tag = $this->readUInt16(substr($entry, 0, 2), $littleEndian);
            $type = $this->readUInt16(substr($entry, 2, 2), $littleEndian);
            $count = $this->readUInt32(substr($entry, 4, 4), $littleEndian);
            if ($tag !== 0x0112 || $type !== 3 || $count === null || $count < 1) {
                continue;
            }

            $valueBytes = substr($entry, 8, 4);
            $value = $littleEndian
                ? (ord($valueBytes[0]) | (ord($valueBytes[1]) << 8))
                : ((ord($valueBytes[0]) << 8) | ord($valueBytes[1]));

            return ($value >= 1 && $value <= 8) ? $value : null;
        }

        return null;
    }

    private function readUInt16(string $bytes, bool $littleEndian): ?int
    {
        if (strlen($bytes) !== 2) {
            return null;
        }

        return $littleEndian
            ? (ord($bytes[0]) | (ord($bytes[1]) << 8))
            : ((ord($bytes[0]) << 8) | ord($bytes[1]));
    }

    private function readUInt32(string $bytes, bool $littleEndian): ?int
    {
        if (strlen($bytes) !== 4) {
            return null;
        }

        if ($littleEndian) {
            return ord($bytes[0]) | (ord($bytes[1]) << 8) | (ord($bytes[2]) << 16) | (ord($bytes[3]) << 24);
        }

        return (ord($bytes[0]) << 24) | (ord($bytes[1]) << 16) | (ord($bytes[2]) << 8) | ord($bytes[3]);
    }

    private function resizeToMaxDimension(\GdImage $image, int $maxDimension): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $maxSide = max($width, $height);

        if ($maxSide <= $maxDimension) {
            return $image;
        }

        $ratio = $maxDimension / $maxSide;
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        // Prevent fatal OOM on low memory environments: skip resize if unsafe.
        if (!$this->canAllocateForResize($newWidth, $newHeight)) {
            return $image;
        }

        $canvas = imagecreatetruecolor($newWidth, $newHeight);
        if ($canvas === false) {
            return $image;
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);

        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $canvas;
    }

    private function canAllocateForResize(int $width, int $height): bool
    {
        $limit = $this->toBytes((string) ini_get('memory_limit'));
        if ($limit <= 0) {
            return true;
        }

        // GD truecolor uses roughly 4 bytes/pixel; keep conservative overhead.
        $estimatedBytes = (int) ceil($width * $height * 5.0);
        $currentUsage = memory_get_usage(true);

        return ($currentUsage + $estimatedBytes) < (int) floor($limit * 0.9);
    }

    private function toBytes(string $value): int
    {
        $value = trim(strtolower($value));
        if ($value === '' || $value === '-1') {
            return -1;
        }

        $number = (int) $value;
        if (str_ends_with($value, 'g')) {
            return $number * 1024 * 1024 * 1024;
        }
        if (str_ends_with($value, 'm')) {
            return $number * 1024 * 1024;
        }
        if (str_ends_with($value, 'k')) {
            return $number * 1024;
        }

        return $number;
    }

    private function encodeWebpToTempFile(\GdImage $image, int $quality): ?string
    {
        $tmpPath = tempnam(sys_get_temp_dir(), 'webp_');
        if ($tmpPath === false) {
            return null;
        }

        $ok = imagewebp($image, $tmpPath, $quality);
        if ($ok !== true || !is_file($tmpPath)) {
            @unlink($tmpPath);
            return null;
        }

        return $tmpPath;
    }
}
