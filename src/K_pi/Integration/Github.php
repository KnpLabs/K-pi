<?php

declare(strict_types=1);

namespace K_pi\Integration;

use K_pi\Data\Github\StatusState;

interface Github
{
    /**
     * @return array{id: string, body: string}
     */
    public function readDiscussion(
        string $owner,
        string $repository,
        int $number,
    ): array;

    public function writeDiscussion(string $id, string $body): void;

    /**
     * @param positive-int     $pullRequest
     * @param non-empty-string $checkName
     */
    public function createCheckRun(
        string $owner,
        string $repository,
        int $pullRequest,
        string $checkName,
    ): void;

    /**
     * @param positive-int     $pullRequest
     * @param non-empty-string $context
     * @param non-empty-string $description
     */
    public function createStatus(
        string $owner,
        string $repository,
        int $pullRequest,
        StatusState $state,
        string $context,
        string $description,
    ): void;
}
