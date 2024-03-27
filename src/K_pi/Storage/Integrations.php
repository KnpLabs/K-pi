<?php

declare(strict_types=1);

namespace K_pi\Storage;

use K_pi\Data\StorageIntegration;
use K_pi\Integration\Github;

final class Integrations
{
    public function __construct(
        private readonly Github\Discussion\Storage\Factory $githubDiscussion,
    ) {}

    public function get(StorageIntegration $integration): Factory
    {
        return match ($integration) {
            StorageIntegration::GITHUB_DISCUSSION => $this->githubDiscussion,
        };
    }
}
