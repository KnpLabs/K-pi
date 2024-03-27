<?php

declare(strict_types=1);

namespace K_pi\Integration\Github;

use Exception;
use InvalidArgumentException;
use K_pi\Data\Github\ResourceUrl;
use K_pi\EnvVars;

final class Variables
{
    private const VARS_GITHUB_TOKEN = [
        'INPUT_GITHUB_TOKEN', // Github Actions
    ];

    private const VARS_PULL_REQUEST = [
        'GITHUB_PULL_REQUEST', // CircleCI
        'INPUT_GITHUB_PULL_REQUEST', // Github Actions
    ];

    public function __construct(private readonly EnvVars $envVars) {}

    public function getToken(): string
    {
        return $this->oneOf(self::VARS_GITHUB_TOKEN);
    }

    /**
     * @throw InvalidArgumentException|Exception
     */
    public function getPullRequestUrl(): ResourceUrl
    {
        $pullRequestUrl = new ResourceUrl(
            $this->oneOf(self::VARS_PULL_REQUEST),
        );

        if ('pull' !== $pullRequestUrl->type) {
            throw new Exception('Not a pull-request url');
        }

        return $pullRequestUrl;
    }

    /**
     * @param non-empty-array<non-empty-string> $variables
     */
    private function oneOf(array $variables): string
    {
        foreach ($variables as $variable) {
            if (false === $this->envVars->has($variable)) {
                continue;
            }

            return $this->envVars->get($variable);
        }

        throw new Exception(
            sprintf(
                'None of the following environment variables were found: %s',
                implode(',', $variables),
            ),
        );
    }
}
