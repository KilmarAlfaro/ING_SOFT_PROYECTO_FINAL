<?php

namespace App\Services;

class DuiService
{
    /**
     * Remove any non-digit characters from the provided DUI string.
     */
    public static function digits(string $dui): string
    {
        return preg_replace('/\D+/', '', $dui);
    }

    /**
     * Format the DUI with the canonical Salvadoran mask (########-#).
     */
    public static function format(string $dui): string
    {
        $digits = self::digits($dui);
        if (strlen($digits) !== 9) {
            return $dui;
        }

        return substr($digits, 0, 8) . '-' . substr($digits, 8, 1);
    }

    /**
     * Validate the DUI according to Salvadoran rules (8 digits + check digit).
     */
    public static function isValid(string $dui): bool
    {
        $digits = self::digits($dui);
        if (strlen($digits) !== 9) {
            return false;
        }

        $body = substr($digits, 0, 8);
        $checkDigit = intval(substr($digits, -1));
        $weights = [9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        foreach (str_split($body) as $index => $char) {
            $sum += intval($char) * $weights[$index];
        }

        $mod = $sum % 10;
        $calculated = (10 - $mod) % 10;

        return $calculated === $checkDigit;
    }
}
