<?php

declare(strict_types=1);

namespace K_pi;

final class ValueNormalizer
{
    public static function normalize(
        float|int $value,
        int $precision,
    ): float|int {
        if (0 == $value) {
            return 0;
        }

        $value = round($value, $precision);

        if ((int) $value == $value) {
            return (int) $value;
        }

        return $value;
    }
}
