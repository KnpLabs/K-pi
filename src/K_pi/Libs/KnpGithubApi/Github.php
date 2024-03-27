<?php

declare(strict_types=1);

namespace K_pi\Libs\KnpGithubApi;

use Exception;
use Github\Client;
use K_pi;
use K_pi\Data\Github\StatusState;

final class Github implements K_pi\Integration\Github
{
    public function __construct(private readonly Client $github) {}

    public function readDiscussion(
        string $owner,
        string $repository,
        int $number,
    ): array {
        $response = $this->github->graphql()->execute(
            <<<'GRAPHQL'
                query($owner: String!, $repository: String!, $number: Int!) {
                  repository(owner: $owner, name: $repository) {
                    discussion(number: $number) {
                      id
                      body
                    }
                  }
                }
                GRAPHQL
            ,
            [
                'owner'      => $owner,
                'repository' => $repository,
                'number'     => $number,
            ],
        );

        if (isset($response['errors'])) {
            throw new Exception(
                json_encode($response['errors'], JSON_THROW_ON_ERROR),
            );
        }

        $id = $response['data']['repository']['discussion']['id'] ?? null;

        if (false === \is_string($id)) {
            throw new Exception(
                'Unable to get discussion ID from ' .
                    json_encode($response, JSON_THROW_ON_ERROR),
            );
        }

        $body = $response['data']['repository']['discussion']['body'] ?? null;

        if (false === \is_string($body)) {
            throw new Exception(
                'Unable to get discussion body from ' .
                    json_encode($response, JSON_THROW_ON_ERROR),
            );
        }

        return ['id' => $id, 'body' => $body];
    }

    public function writeDiscussion(string $id, string $body): void
    {
        $response = $this->github->graphql()->execute(
            <<<'GRAPHQL'
                mutation($id: ID!, $body: String!) {
                  updateDiscussion(input: {discussionId: $id, body: $body}) {
                    clientMutationId
                  }
                }
                GRAPHQL
            ,
            [
                'id'   => $id,
                'body' => $body,
            ],
        );

        if (isset($response['errors'])) {
            throw new Exception(
                json_encode($response['errors'], JSON_THROW_ON_ERROR),
            );
        }
    }

    public function createCheckRun(
        string $owner,
        string $repository,
        int $pullRequest,
        string $checkName,
    ): void {
        $response = $this->github->graphql()->execute(
            <<<'GRAPHQL'
                query($owner: String!, $repository: String!, $pullRequest: Int!) {
                  repository(owner: $owner, name: $repository) {
                    id
                    pullRequest(number: $pullRequest) {
                      headRefOid
                    }
                  }
                }
                GRAPHQL
            ,
            [
                'owner'       => $owner,
                'repository'  => $repository,
                'pullRequest' => $pullRequest,
            ],
        );

        $repositoryId = $response['data']['repository']['id'] ?? null;

        if (false === \is_string($repositoryId)) {
            throw new Exception(
                'Unable to get repository ID from ' . json_encode($response),
            );
        }

        $commitHash = $response['data']['repository']['pullRequest']['headRefOid'] ??
            null;

        if (false === \is_string($commitHash)) {
            throw new Exception(
                'Unable to get pull-request commit hash from ' .
                    json_encode($response),
            );
        }

        $this->github->graphql()->execute(
            <<<'GRAPHQL'
                mutation ($repositoryId: ID!, $checkName: String!, $commitHash: GitObjectID!, $output: CheckRunOutput!) {
                  createCheckRun(input: {repositoryId: $repositoryId, name: $checkName, headSha: $commitHash, status: COMPLETED, conclusion: NEUTRAL, output: $output}) {
                    clientMutationId
                  }
                }
                GRAPHQL
            ,
            [
                'repositoryId' => $repositoryId,
                'commitHash'   => $commitHash,
                'checkName'    => $checkName,
                'output'       => [
                    'title'   => 'I need some burger',
                    'summary' => 'we are living in a yellow summary',
                ],
            ],
        );
    }

    public function createStatus(
        string $owner,
        string $repository,
        int $pullRequest,
        StatusState $state,
        string $context,
        string $description,
    ): void {
        $response = $this->github->graphql()->execute(
            <<<'GRAPHQL'
                query($owner: String!, $repository: String!, $pullRequest: Int!) {
                  repository(owner: $owner, name: $repository) {
                    pullRequest(number: $pullRequest) {
                      headRefOid
                    }
                  }
                }
                GRAPHQL
            ,
            [
                'owner'       => $owner,
                'repository'  => $repository,
                'pullRequest' => $pullRequest,
            ],
        );

        $commitHash = $response['data']['repository']['pullRequest']['headRefOid'] ??
            null;

        if (false === \is_string($commitHash)) {
            throw new Exception(
                'Unable to get pull-request commit hash from ' .
                    json_encode($response),
            );
        }

        $this->github->repository()->statuses()->create(
            username: $owner,
            repository: $repository,
            sha: $commitHash,
            params: [
                'context'     => $context,
                'description' => $description,
                'state'       => $state->value,
            ],
        );
    }
}
