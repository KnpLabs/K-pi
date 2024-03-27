<?php

declare(strict_types=1);

namespace K_pi\Storage;

use K_pi\Storage;

interface Factory
{
    /**
     * @param non-empty-string $reportName
     */
    public function build(string $reportName, mixed $configuration): Storage;
}
