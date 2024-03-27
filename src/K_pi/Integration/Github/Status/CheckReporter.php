<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\Status;

use K_pi\CheckReporter as CheckReporterInterface;
use K_pi\Data\Diff;
use K_pi\Data\Github\StatusState;
use K_pi\Integration\Github;
use K_pi\Integration\Github\Status\CheckReporter\Configuration;
use K_pi\Integration\Github\Variables;

final class CheckReporter implements CheckReporterInterface
{
    public function __construct(
        private readonly Github $github,
        private readonly Variables $variables,
        private readonly Configuration $configuration,
    ) {}

    public function send(Diff ...$notifications): void
    {
        if ([] === $notifications) {
            return;
        }

        $pullRequest = $this->variables->getPullRequestUrl();

        foreach ($notifications as $notification) {
            if (false === $notification->changed) {
                continue;
            }

            $description = sprintf(
                '%s%s (%s %s%s%s)',
                $notification->to,
                $this->getUnit($notification->to),
                $this->getIndicator($notification->diff),
                $this->getSign($notification->diff),
                $notification->diff,
                $this->getUnit($notification->diff),
            );

            $this->github->createStatus(
                owner: $pullRequest->owner,
                repository: $pullRequest->repository,
                pullRequest: $pullRequest->number,
                state: $this->getState($notification->diff),
                context: $this->configuration->reportName .
                    ': ' .
                    $notification->name,
                description: $description,
            );
        }
    }

    private function getState(float|int $value): StatusState
    {
        if ($value >= 0) {
            return $this->configuration->onHigher;
        }

        return $this->configuration->onLower;
    }

    private function getIndicator(float|int $value): ?string
    {
        if ($value > 0) {
            return '⬈';
        }

        if ($value < 0) {
            return '⬊';
        }

        return null;
    }

    private function getUnit(float|int $value): ?string
    {
        if ($value >= 2) {
            return $this->configuration->pluralUnit;
        }

        if ($value <= -2) {
            return $this->configuration->pluralUnit;
        }

        return $this->configuration->singularUnit;
    }

    private function getSign(float|int $value): ?string
    {
        if ($value >= 0) {
            return '+';
        }

        return null;
    }
}
