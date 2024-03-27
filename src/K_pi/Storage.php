<?php

declare(strict_types=1);

namespace K_pi;

use K_pi\Configuration\ReportConfiguration;
use K_pi\Data\Report;

interface Storage
{
    public function read(): Report;

    public function write(
        Report $report,
        ReportConfiguration $configuration,
    ): void;
}
