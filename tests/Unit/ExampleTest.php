<?php

namespace Tests\Unit;

use App\Services\VietQrService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_vietqr_payload_contains_dynamic_amount_and_valid_crc(): void
    {
        $payload = (new VietQrService())->payload('970422', '06030082005', 272000, 'SKINFOOD DH2 ABC123');

        $this->assertStringStartsWith('000201010212', $payload);
        $this->assertStringContainsString('5406272000', $payload);
        $this->assertMatchesRegularExpression('/6304[0-9A-F]{4}$/', $payload);
    }

    public function test_vietqr_rejects_invalid_bank_bin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new VietQrService())->payload('123', '06030082005', 100000, 'SKINFOOD TEST');
    }
}
