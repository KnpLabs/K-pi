<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\CheckRun;

use K_pi\CheckReporter as CheckReporterInterface;
use K_pi\Data\Diff;
use K_pi\Integration\Github;
use K_pi\Integration\Github\Variables;

final class CheckReporter implements CheckReporterInterface
{
    public function __construct(
        private readonly Github $github,
        private readonly Variables $variables,
    ) {}

    public function send(Diff ...$notifications): void
    {
        if ([] === $notifications) {
            return;
        }

        $pullRequest = $this->variables->getPullRequestUrl();

        foreach ($notifications as $notification) {
            $this->github->createCheckRun(
                owner: $pullRequest->owner,
                repository: $pullRequest->repository,
                pullRequest: $pullRequest->number,
                checkName: "C'est ouf",
            );
        }
    }
}
