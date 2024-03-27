<?php

declare(strict_types=1);

namespace K_pi\CheckReporter;

use K_pi\Data\CheckReporterIntegration;
use K_pi\Integration\Github;

final class Integrations
{
    public function __construct(
        private readonly Github\CheckRun\CheckReporter\Factory $githubCheckRun,
        private readonly Github\Status\CheckReporter\Factory $githubStatus,
    ) {}

    public function get(CheckReporterIntegration $integration): Factory
    {
        return match ($integration) {
            CheckReporterIntegration::GITHUB_CHECK_RUN => $this->githubCheckRun,
            CheckReporterIntegration::GITHUB_STATUS    => $this->githubStatus,
        };
    }
}
