<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\CheckRun\CheckReporter;

use K_pi\CheckReporter\Factory as FactoryInterface;
use K_pi\Integration\Github;
use K_pi\Integration\Github\CheckRun\CheckReporter;
use K_pi\Integration\Github\Variables;

final class Factory implements FactoryInterface
{
    public function __construct(
        private readonly Github $github,
        private readonly Variables $variables,
    ) {}

    public function build(
        mixed $configuration,
        string $reportName,
    ): CheckReporter {
        return new CheckReporter($this->github, $this->variables);
    }
}
