<?php

namespace App\Services;

class PhoneNormalizerService
{
    public function normalizeIndonesia(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', trim($phone)) ?? '';
        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62' . substr($digits, 1);
        }

        return '62' . $digits;
    }
}
