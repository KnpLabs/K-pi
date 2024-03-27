<?php

declare(strict_types=1);

namespace K_pi;

use K_pi\Configuration\Exception\AtPathException;
use K_pi\Configuration\ReportConfiguration;

final class Configuration
{
    public function __construct(private readonly object $configuration) {}

    /**
     * @param non-empty-string $reportName
     */
    public function get(string $reportName): ReportConfiguration
    {
        if (false === property_exists($this->configuration, 'reports')) {
            throw new AtPathException('.', 'property "reports" is mandatory');
        }

        if (false === \is_object($this->configuration->reports)) {
            throw new AtPathException('.reports', 'must be an object');
        }

        if (
            false ===
            property_exists($this->configuration->reports, $reportName)
        ) {
            throw new AtPathException(
                '.reports',
                sprintf('property "%s" not found', $reportName),
            );
        }

        if (
            false === \is_object($this->configuration->reports->{$reportName})
        ) {
            throw new AtPathException(
                sprintf('.reports.%s', $reportName),
                'must be an object',
            );
        }

        return new ReportConfiguration(
            $this->configuration->reports->{$reportName},
            $reportName,
        );
    }
}
