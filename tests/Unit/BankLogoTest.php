<?php

namespace Tests\Unit;

use App\Support\BankLogo;
use Tests\TestCase;

class BankLogoTest extends TestCase
{
    public function test_maps_common_bank_aliases_to_expected_logo_files(): void
    {
        $this->assertSame('bca.svg', BankLogo::fileName('BCA'));
        $this->assertSame('bca.svg', BankLogo::fileName('Bank Central Asia'));
        $this->assertSame('bni.svg', BankLogo::fileName('bni 46'));
        $this->assertSame('bri.svg', BankLogo::fileName('BANK RAKYAT INDONESIA'));
        $this->assertSame('mandiri.svg', BankLogo::fileName('Bank Mandiri'));
    }

    public function test_returns_default_logo_for_unknown_or_empty_bank_name(): void
    {
        $this->assertSame('default.svg', BankLogo::fileName('Bank Tidak Dikenal'));
        $this->assertSame('default.svg', BankLogo::fileName(''));
        $this->assertSame('default.svg', BankLogo::fileName(null));
    }
}

