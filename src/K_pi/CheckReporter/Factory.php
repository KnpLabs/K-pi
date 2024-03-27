<?php

declare(strict_types=1);

namespace K_pi\CheckReporter;

use K_pi\CheckReporter;

interface Factory
{
    /**
     * @param non-empty-string $reportName
     */
    public function build(
        mixed $configuration,
        string $reportName,
    ): CheckReporter;
}
