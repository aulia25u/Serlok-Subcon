<?php

namespace App\Services;

use Illuminate\Support\Str;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Google2FAService
{
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random base32 secret key for Google Authenticator.
     */
    public function generateSecretKey(int $length = 16): string
    {
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, strlen(self::BASE32_CHARS) - 1)];
        }

        return $secret;
    }

    /**
     * Return the QR code URL that can be scanned by Google Authenticator.
     */
    public function getQrCodeUrl(string $issuer, string $username, string $secret): string
    {
        $otpAuthUrl = $this->getOtpAuthUrl($issuer, $username, $secret);

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrCode = $writer->writeString($otpAuthUrl);

        return 'data:image/svg+xml;base64,' . base64_encode($qrCode);
    }

    /**
     * Return the OTPAuth URI used by authenticator apps.
     */
    public function getOtpAuthUrl(string $issuer, string $username, string $secret): string
    {
        $label = rawurlencode($issuer . ':' . $username);

        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            $label,
            $secret,
            rawurlencode($issuer)
        );
    }

    /**
     * Verify a provided one-time code against a stored secret.
     */
    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);

        if (! ctype_digit($code) || strlen($code) !== 6) {
            return false;
        }

        $secret = strtoupper($secret);
        $current = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->calculateOtp($secret, $current + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    protected function calculateOtp(string $secret, int $timestamp): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N2', 0, $timestamp);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $binary = (ord($hash[$offset]) & 0x7f) << 24;
        $binary |= (ord($hash[$offset + 1]) & 0xff) << 16;
        $binary |= (ord($hash[$offset + 2]) & 0xff) << 8;
        $binary |= ord($hash[$offset + 3]) & 0xff;

        return str_pad((string) ($binary % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Decode a base32 string without relying on external libraries.
     */
    protected function base32Decode(string $secret): string
    {
        $secret = strtoupper($secret);
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        foreach (str_split($secret) as $char) {
            if ($char === '=') {
                break;
            }

            $value = strpos(self::BASE32_CHARS, $char);

            if ($value === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }

        return $output;
    }
}
