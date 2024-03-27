<?php

declare(strict_types=1);

namespace K_pi\Configuration;

use K_pi\Configuration\Exception\AtPathException;
use K_pi\Data\CheckReporterIntegration;
use K_pi\Data\Extra;
use K_pi\Data\StorageIntegration;

final class ReportConfiguration
{
    public function __construct(
        private readonly object $configuration,
        private readonly string $reportName,
    ) {}

    /**
     * @return int<0, max>
     */
    public function getPrecision(): int
    {
        if (false === property_exists($this->configuration, 'precision')) {
            return 2;
        }

        $precision = $this->configuration->precision;

        if (false === \is_int($precision) || 0 > $precision) {
            throw new AtPathException(
                sprintf('.reports.%s.precision', $this->reportName),
                'zero or positive integer expected',
            );
        }

        return $precision;
    }

    /**
     * @return array{StorageIntegration, mixed}
     */
    public function getStorageConfiguration(): array
    {
        if (false === property_exists($this->configuration, 'storage')) {
            throw new AtPathException(
                sprintf('.reports.%s', $this->reportName),
                'property "storage" is mandatory',
            );
        }

        foreach (
            get_object_vars($this->configuration->storage) as $integrationName => $integrationConfiguration
        ) {
            $integration = StorageIntegration::tryFrom($integrationName);

            if (null === $integration) {
                throw new AtPathException(
                    sprintf('.reports.%s.storage', $this->reportName),
                    sprintf(
                        'integration "%s" does not exists, must be %s',
                        $integration,
                        implode(
                            ' or ',
                            array_map(
                                static fn (
                                    StorageIntegration $integration,
                                ) => '"' . $integration->value . '"',
                                StorageIntegration::cases(),
                            ),
                        ),
                    ),
                );
            }

            return [$integration, $integrationConfiguration];
        }

        throw new AtPathException(
            sprintf('.reports.%s.storage', $this->reportName),
            sprintf(
                'integration is mandatory, must be %s',
                implode(
                    ' or ',
                    array_map(
                        static fn (StorageIntegration $integration) => '"' .
                            $integration->value .
                            '"',
                        StorageIntegration::cases(),
                    ),
                ),
            ),
        );
    }

    /**
     * @return iterable<CheckReporterIntegration, mixed>
     */
    public function getCheckReportersConfiguration(): iterable
    {
        $configuration = get_object_vars($this->configuration);

        if (false === \array_key_exists('check-reporter', $configuration)) {
            return [];
        }

        $checkReporters = $configuration['check-reporter'];

        if (false === \is_object($checkReporters)) {
            throw new AtPathException(
                sprintf('.reports.%s.check-reporter', $this->reportName),
                'object expected',
            );
        }

        $empty = true;

        foreach (
            get_object_vars($checkReporters) as $integrationName => $integrationConfiguration
        ) {
            $integration = CheckReporterIntegration::tryFrom($integrationName);

            if (null === $integration) {
                throw new AtPathException(
                    sprintf('.reports.%s.check-reporter', $this->reportName),
                    sprintf(
                        'integration "%s" does not exists, must be %s',
                        $integration,
                        implode(
                            ' or ',
                            array_map(
                                static fn (
                                    CheckReporterIntegration $integration,
                                ) => '"' . $integration->value . '"',
                                CheckReporterIntegration::cases(),
                            ),
                        ),
                    ),
                );
            }

            $empty = false;

            yield $integration => $integrationConfiguration;
        }

        if ($empty) {
            throw new AtPathException(
                sprintf('.reports.%s.check-reporter', $this->reportName),
                sprintf(
                    'integration is mandatory, must be %s',
                    implode(
                        ' or ',
                        array_map(
                            static fn (
                                CheckReporterIntegration $integration,
                            ) => '"' . $integration->value . '"',
                            CheckReporterIntegration::cases(),
                        ),
                    ),
                ),
            );
        }
    }

    /**
     * @return iterable<non-empty-string, non-empty-string>
     */
    public function getColors(): iterable
    {
        if (false === property_exists($this->configuration, 'colors')) {
            return;
        }

        $colors = $this->configuration->colors;

        if (false === \is_object($colors)) {
            throw new AtPathException(
                sprintf('.reports.%s.colors', $this->reportName),
                'non empty object is expected',
            );
        }

        $empty = true;

        foreach (get_object_vars($colors) as $name => $value) {
            $empty = false;

            if ('' === $name) {
                throw new AtPathException(
                    sprintf('.reports.%s.colors', $this->reportName),
                    'property name must be non-empty string',
                );
            }

            if (false === \is_string($value) || '' === $value) {
                throw new AtPathException(
                    sprintf('.reports.%s.colors.%s', $this->reportName, $name),
                    'color must be non-empty string',
                );
            }

            yield $name => $value;
        }

        if ($empty) {
            throw new AtPathException(
                sprintf('.reports.%s.colors', $this->reportName),
                'non empty object is expected',
            );
        }
    }

    /**
     * @return iterable<non-empty-string, Extra>
     */
    public function getExtra(): iterable
    {
        if (false === property_exists($this->configuration, 'extra')) {
            return;
        }

        $extra = $this->configuration->extra;

        if (false === \is_object($extra)) {
            throw new AtPathException(
                sprintf('.reports.%s.extra', $this->reportName),
                'non empty object is expected',
            );
        }

        $empty = true;

        foreach (get_object_vars($extra) as $name => $value) {
            $empty = false;

            if ('' === $name) {
                throw new AtPathException(
                    sprintf('.reports.%s.extra', $this->reportName),
                    'property name must be non-empty string',
                );
            }

            if (false === \is_string($value)) {
                throw new AtPathException(
                    sprintf('.reports.%s.extra.%s', $this->reportName, $name),
                    'must be a string',
                );
            }

            $enum = Extra::tryFrom($value);

            if (null === $enum) {
                throw new AtPathException(
                    sprintf('.reports.%s.extra.%s', $this->reportName, $name),
                    sprintf(
                        'extra "%s" does not exists, must be %s',
                        $value,
                        implode(
                            ' or ',
                            array_map(
                                static fn (Extra $extra) => '"' .
                                    $extra->value .
                                    '"',
                                Extra::cases(),
                            ),
                        ),
                    ),
                );
            }

            yield $name => $enum;
        }

        if ($empty) {
            throw new AtPathException(
                sprintf('.reports.%s.extra', $this->reportName),
                'non empty object is expected',
            );
        }
    }
}
