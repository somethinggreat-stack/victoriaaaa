<?php

namespace App\Support;

/**
 * Small set of helpers for masking PII when the read-only reviewer session
 * is viewing list pages. The reviewer should see columns and activity volume
 * but never see real customer email addresses, full names, or phone numbers.
 */
final class Mask
{
    public static function email(?string $email): string
    {
        if (!is_string($email) || trim($email) === '') {
            return '—';
        }
        $parts = explode('@', $email, 2);
        if (count($parts) !== 2) {
            return '•••';
        }
        [$local, $domain] = $parts;

        $localMasked = self::keepLeading($local, 2);

        $dot = strrpos($domain, '.');
        if ($dot === false) {
            $domainMasked = self::keepLeading($domain, 1);
        } else {
            $name = substr($domain, 0, $dot);
            $tld  = substr($domain, $dot);
            $domainMasked = self::keepLeading($name, 1) . $tld;
        }

        return $localMasked . '@' . $domainMasked;
    }

    public static function name(?string $first, ?string $last = null): string
    {
        $first = trim((string) $first);
        $last  = trim((string) $last);

        if ($first === '' && $last === '') {
            return '—';
        }

        $f = $first !== '' ? self::keepLeading($first, 1) : '';
        $l = $last  !== '' ? mb_substr($last, 0, 1) . '.' : '';

        return trim($f . ' ' . $l);
    }

    public static function phone(?string $phone): string
    {
        if (!is_string($phone) || trim($phone) === '') {
            return '—';
        }
        $digits = preg_replace('/\D+/', '', $phone);
        $len = strlen($digits);
        if ($len === 0) {
            return '•••';
        }
        if ($len <= 4) {
            return str_repeat('•', $len);
        }
        return str_repeat('•', $len - 4) . substr($digits, -4);
    }

    public static function snippet(?string $text): string
    {
        if (!is_string($text) || trim($text) === '') {
            return '—';
        }
        return '[message hidden in review mode]';
    }

    private static function keepLeading(string $value, int $keep): string
    {
        $len = mb_strlen($value);
        if ($len <= $keep) {
            return mb_substr($value, 0, $len);
        }
        return mb_substr($value, 0, $keep) . str_repeat('•', max(1, $len - $keep));
    }
}
