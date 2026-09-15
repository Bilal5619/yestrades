<?php

namespace App\Support;

class Vat
{
    public const RATE = 20.0;

    /**
     * All YesTrades prices are VAT-inclusive.
     *
     * Example:
     * Gross £65.00
     * Net   £54.17
     * VAT   £10.83
     */
    public static function fromInclusive($gross): array
    {
        $gross = round((float) $gross, 2);

        $net = round(
            $gross / (1 + (self::RATE / 100)),
            2
        );

        $vat = round($gross - $net, 2);

        return [
            'gross' => $gross,
            'net' => $net,
            'vat' => $vat,
            'rate' => self::RATE,
        ];
    }
}