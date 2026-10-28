<?php

namespace App\Services;

use InvalidArgumentException;

class VietQrService
{
    public function quickLink(string $bankId, string $accountNumber, string $accountName, int $amount, string $description): string
    {
        $bankId = preg_replace('/[^A-Za-z0-9]/', '', $bankId);
        $accountNumber = preg_replace('/[^A-Za-z0-9]/', '', $accountNumber);
        if ($bankId === '' || $accountNumber === '') {
            throw new InvalidArgumentException('Thiếu thông tin ngân hàng để tạo VietQR Quick Link.');
        }

        $baseUrl = rtrim((string) config('services.bank_transfer.vietqr_image_url', 'https://img.vietqr.io/image'), '/');
        $query = http_build_query([
            'amount' => max(0, $amount),
            'addInfo' => $description,
            'accountName' => $accountName,
        ], '', '&', PHP_QUERY_RFC3986);

        return $baseUrl . '/' . $bankId . '-' . $accountNumber . '-compact2.png?' . $query;
    }

    private function field(string $id, string $value): string
    {
        $length = strlen($value);
        if (!preg_match('/^\d{2}$/', $id) || $length > 99) {
            throw new InvalidArgumentException('Dữ liệu VietQR không hợp lệ.');
        }

        return $id . str_pad((string) $length, 2, '0', STR_PAD_LEFT) . $value;
    }

    private function crc16(string $data): int
    {
        $crc = 0xFFFF;
        for ($i = 0, $length = strlen($data); $i < $length; $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($bit = 0; $bit < 8; $bit++) {
                $crc = (($crc & 0x8000) !== 0) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
            }
            $crc &= 0xFFFF;
        }

        return $crc;
    }

    public function payload(string $bankBin, string $accountNumber, int $amount, string $description): string
    {
        $bankBin = preg_replace('/\D/', '', $bankBin);
        $accountNumber = preg_replace('/[^A-Za-z0-9]/', '', $accountNumber);
        if (!preg_match('/^\d{6}$/', $bankBin)) {
            throw new InvalidArgumentException('BIN ngân hàng phải gồm 6 chữ số.');
        }
        if (!preg_match('/^[A-Za-z0-9]{4,19}$/', $accountNumber)) {
            throw new InvalidArgumentException('Số tài khoản nhận chuyển khoản không hợp lệ.');
        }

        $beneficiary = $this->field('00', $bankBin) . $this->field('01', $accountNumber);
        $merchant = $this->field('00', 'A000000727')
            . $this->field('01', $beneficiary)
            . $this->field('02', 'QRIBFTTA');
        $payload = $this->field('00', '01')
            . $this->field('01', '12')
            . $this->field('38', $merchant)
            . $this->field('53', '704');
        if ($amount > 0) {
            $payload .= $this->field('54', (string) $amount);
        }
        $payload .= $this->field('58', 'VN');
        if ($description !== '') {
            $payload .= $this->field('62', $this->field('08', $description));
        }
        $payload .= '6304';

        return $payload . strtoupper(str_pad(dechex($this->crc16($payload)), 4, '0', STR_PAD_LEFT));
    }
}
