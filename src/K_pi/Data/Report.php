<?php

declare(strict_types=1);

namespace K_pi\Data;

use DateTimeImmutable;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<non-empty-string, non-empty-array<non-empty-string, float|int>>
 */
final class Report implements IteratorAggregate
{
    /**
     * @var array<non-empty-string, non-empty-array<non-empty-string, float|int>>
     */
    private array $datasets = [];

    /**
     * @var array<non-empty-string, non-empty-string>
     */
    private array $colors = [];

    public function getIterator(): Traversable
    {
        yield from $this->datasets;
    }

    /**
     * @param non-empty-string $name
     */
    public function add(
        string $name,
        DateTimeImmutable $date,
        float|int $value,
    ): void {
        $index = $date->format('Y-m-d');

        $this->datasets[$name][$index] = $value;

        ksort($this->datasets[$name]);

        $current = null;

        foreach ($this->datasets[$name] as $date => $value) {
            if ($value === $current) {
                $dataset = $this->datasets[$name];

                unset($dataset[$date]);

                if ([] === $dataset) {
                    unset($this->datasets[$name]);
                } else {
                    $this->datasets[$name] = $dataset;
                }
            }

            $current = $value;
        }
    }

    /**
     * @param non-empty-string $name
     * @param non-empty-string $color
     */
    public function colorize(string $name, string $color): void
    {
        $this->colors[$name] = $color;
    }

    /**
     * @param non-empty-string $name
     *
     * @return ?non-empty-string
     */
    public function getColor(string $name): ?string
    {
        return $this->colors[$name] ?? null;
    }

    /**
     * @param non-empty-string $name
     *
     * @return null|float|int
     */
    public function last(string $name): mixed
    {
        if (false === \array_key_exists($name, $this->datasets)) {
            return null;
        }

        return end($this->datasets[$name]);
    }

    /**
     * @return array<non-empty-string, float|int>
     */
    public function getExtra(Extra $extra): array
    {
        return match ($extra) {
            Extra::TOTAL => $this->total(),
        };
    }

    /**
     * @return array<non-empty-string, float|int>
     */
    private function total(): array
    {
        $totals = [];

        foreach ($this->datasets as $name => $dataset) {
            foreach ($dataset as $date => $value) {
                $totals[$date][$name] = $value;
            }
        }

        ksort($totals);

        $current = [];

        foreach ($totals as $date => $total) {
            $current = $totals[$date] = array_merge($current, $total);
        }

        return array_map(array_sum(...), $totals);
    }
}
