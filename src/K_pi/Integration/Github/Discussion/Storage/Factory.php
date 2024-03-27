<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\Discussion\Storage;

use K_pi\Integration\Github;
use K_pi\Integration\Github\Discussion\Storage;
use K_pi\Storage\Factory as FactoryInterface;

final class Factory implements FactoryInterface
{
    public function __construct(private readonly Github $github) {}

    public function build(string $reportName, mixed $configuration): Storage
    {
        return new Storage(
            new Configuration($configuration, $reportName),
            $this->github,
        );
    }
}
