<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\Discussion\Storage;

use Exception;
use K_pi\Configuration\Exception\AtPathException;
use K_pi\Data\Github\ResourceUrl;
use K_pi\Data\StorageIntegration;
use Throwable;

final class Configuration
{
    public readonly ResourceUrl $discussion;

    public readonly bool $report;

    public readonly bool $persist;

    public function __construct(
        mixed $configuration,
        private readonly string $reportName,
    ) {
        if (false === \is_object($configuration)) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.storage.%s',
                    $this->reportName,
                    StorageIntegration::GITHUB_DISCUSSION->value,
                ),
                'object is expected.',
            );
        }

        $this->discussion = $this->parseUrl($configuration);
        $this->report     = $this->parseBool($configuration, 'report', true);
        $this->persist    = $this->parseBool($configuration, 'persist', true);
    }

    private function parseUrl(object $configuration): ResourceUrl
    {
        if (false === property_exists($configuration, 'url')) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.storage.%s',
                    $this->reportName,
                    StorageIntegration::GITHUB_DISCUSSION->value,
                ),
                'property "url" is mandatory.',
            );
        }

        try {
            $url = new ResourceUrl($configuration->url);

            if ('discussions' !== $url->type) {
                throw new Exception('invalid Github discussion url');
            }
        } catch (Throwable $previous) {
            throw AtPathException::buildFromThrowable(
                sprintf(
                    '.reports.%s.storage.%s.url',
                    $this->reportName,
                    StorageIntegration::GITHUB_DISCUSSION->value,
                ),
                $previous,
            );
        }

        return $url;
    }

    private function parseBool(
        object $configuration,
        string $key,
        bool $default,
    ): bool {
        if (false === property_exists($configuration, $key)) {
            return $default;
        }

        $bool = $configuration->{$key};

        if (false === \is_bool($bool)) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.storage.%s.%s',
                    $this->reportName,
                    StorageIntegration::GITHUB_DISCUSSION->value,
                    $key,
                ),
                'must be a boolean.',
            );
        }

        return $bool;
    }
}
