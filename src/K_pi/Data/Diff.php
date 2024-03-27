<?php

declare(strict_types=1);

namespace K_pi\Data;

use K_pi\ValueNormalizer;

final class Diff
{
    public readonly float|int $diff;

    public readonly bool $changed;

    /**
     * @param int<0, max> $precision
     */
    public function __construct(
        public readonly string $name,
        public readonly float|int $from,
        public readonly float|int $to,
        int $precision,
    ) {
        $this->diff    = ValueNormalizer::normalize($to - $from, $precision);
        $this->changed = 0 !== $this->diff;
    }
}
