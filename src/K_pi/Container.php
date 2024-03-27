<?php

declare(strict_types=1);

namespace K_pi;

use Exception;

final class Container
{
    /**
     * @var array<class-string, callable(Container): object>
     */
    private array $definitions;

    /**
     * @var array<class-string, object>
     */
    private array $services;

    public function __construct()
    {
        $this->definitions = [];
        $this->services    = [];
    }

    /**
     * @template T of object
     *
     * @param class-string<T>                   $service
     * @param callable(Container $container): T $definition
     */
    public function define(string $service, callable $definition): void
    {
        if (\array_key_exists($service, $this->services)) {
            throw new Exception("Service {$service} is already built.");
        }

        $this->definitions[$service] = $definition;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $service
     *
     * @return T
     */
    public function get(string $service): object
    {
        if (\array_key_exists($service, $this->services)) {
            /**
             * @var T
             */
            return $this->services[$service];
        }

        if (\array_key_exists($service, $this->definitions)) {
            /**
             * @var callable(Container $container): T
             */
            $definition = $this->definitions[$service];

            unset($this->definitions[$service]);

            return $this->services[$service] = $definition($this);
        }

        throw new Exception("Service {$service} not found.");
    }
}
