<?php

namespace App\Support;

class BankLogo
{
    /**
     * @var array<string, array{file: string, aliases: string[]}>
     */
    private const MAP = [
        'bca' => [
            'file' => 'bca.svg',
            'aliases' => ['bca', 'bank central asia', 'central asia'],
        ],
        'bni' => [
            'file' => 'bni.svg',
            'aliases' => ['bni', 'bank negara indonesia', 'bni 46'],
        ],
        'bri' => [
            'file' => 'bri.svg',
            'aliases' => ['bri', 'bank rakyat indonesia', 'britama'],
        ],
        'mandiri' => [
            'file' => 'mandiri.svg',
            'aliases' => ['mandiri', 'bank mandiri', 'livin'],
        ],
        'cimb' => [
            'file' => 'cimb.svg',
            'aliases' => ['cimb', 'cimb niaga', 'bank cimb niaga', 'octo'],
        ],
        'danamon' => [
            'file' => 'danamon.svg',
            'aliases' => ['danamon', 'bank danamon'],
        ],
        'permata' => [
            'file' => 'permata.svg',
            'aliases' => ['permata', 'permatabank', 'permata bank'],
        ],
        'btn' => [
            'file' => 'btn.svg',
            'aliases' => ['btn', 'bank tabungan negara'],
        ],
        'bsi' => [
            'file' => 'bsi.svg',
            'aliases' => ['bsi', 'bank syariah indonesia', 'bank syariah'],
        ],
        'bjb' => [
            'file' => 'bjb.svg',
            'aliases' => ['bjb', 'bank bjb', 'bank jawa barat'],
        ],
    ];

    private const DEFAULT_FILE = 'default.svg';

    public static function assetUrl(?string $bankName): string
    {
        return asset(self::relativePath($bankName));
    }

    public static function relativePath(?string $bankName): string
    {
        $file = self::fileName($bankName);
        $relative = 'assets/banks/' . $file;

        if (is_file(public_path($relative))) {
            return $relative;
        }

        return 'assets/banks/' . self::DEFAULT_FILE;
    }

    public static function fileName(?string $bankName): string
    {
        $normalized = self::normalize($bankName);
        if ($normalized === '') {
            return self::DEFAULT_FILE;
        }

        foreach (self::MAP as $item) {
            foreach ($item['aliases'] as $alias) {
                $needle = self::normalize($alias);
                if ($needle !== '' && str_contains($normalized, $needle)) {
                    return $item['file'];
                }
            }
        }

        return self::DEFAULT_FILE;
    }

    public static function normalize(?string $value): string
    {
        $value = strtolower((string) $value);
        $value = preg_replace('/[^a-z0-9]+/i', ' ', $value) ?? '';
        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }
}

