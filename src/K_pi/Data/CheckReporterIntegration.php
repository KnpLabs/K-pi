<?php

declare(strict_types=1);

namespace K_pi\Data;

enum CheckReporterIntegration: string
{
    case GITHUB_CHECK_RUN = 'github-check-run';

    case GITHUB_STATUS = 'github-status';
}
