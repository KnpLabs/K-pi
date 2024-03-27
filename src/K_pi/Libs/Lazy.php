<?php

declare(strict_types=1);

namespace K_pi\Libs;

/**
 * @template T of object
 */
abstract class Lazy
{
    /**
     * @var callable(): T
     */
    private $loader;

    /**
     * @var T
     */
    private object $loaded;

    /**
     * @param callable(): T $loader
     */
    public function __construct(callable $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return T
     */
    protected function load(): object
    {
        if (false === isset($this->loaded)) {
            $this->loaded = ($this->loader)();
        }

        return $this->loaded;
    }
}
