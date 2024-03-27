<?php

declare(strict_types=1);

namespace K_pi;

use Exception;

final class EnvVars
{
    /**
     * @var array<non-empty-string, string>
     */
    private array $defaults = [];

    /**
     * @param non-empty-string $name
     */
    public function get(string $name): string
    {
        $env = $this->read($name);

        if (null === $env) {
            throw new Exception("{$name} env variable not found.");
        }

        return $env;
    }

    /**
     * @param non-empty-string $name
     */
    public function has(string $name): bool
    {
        return null !== $this->read($name);
    }

    /**
     * @param non-empty-string $name
     */
    public function default(string $name, string $env): void
    {
        $this->defaults[$name] = $env;
    }

    /**
     * @param non-empty-string $name
     */
    private function read(string $name): ?string
    {
        if (\is_string($env = getenv($name))) {
            return $env;
        }

        if (\array_key_exists($name, $this->defaults)) {
            return $this->defaults[$name];
        }

        return null;
    }
}
