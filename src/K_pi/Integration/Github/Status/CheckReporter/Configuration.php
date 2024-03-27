<?php

declare(strict_types=1);

namespace K_pi\Integration\Github\Status\CheckReporter;

use K_pi\Configuration\Exception\AtPathException;
use K_pi\Data\CheckReporterIntegration;
use K_pi\Data\Github\StatusState;
use stdClass;

final class Configuration
{
    private const PREBUILD_STATES = [
        'higher-is-better' => [StatusState::ERROR, StatusState::SUCCESS],
        'lower-is-better'  => [StatusState::SUCCESS, StatusState::ERROR],
    ];

    public readonly ?string $singularUnit;

    public readonly ?string $pluralUnit;

    public readonly StatusState $onLower;

    public readonly StatusState $onHigher;

    /**
     * @param non-empty-string $reportName
     */
    public function __construct(
        mixed $configuration,
        public readonly string $reportName,
    ) {
        if (null === $configuration) {
            $configuration = new stdClass();
        }

        if (false === \is_object($configuration)) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.check-reporter.%s',
                    $this->reportName,
                    CheckReporterIntegration::GITHUB_STATUS->value,
                ),
                'must be null or an object',
            );
        }

        [$this->singularUnit, $this->pluralUnit] = $this->getUnits(
            $configuration,
        );
        [$this->onLower, $this->onHigher] = $this->getStates($configuration);
    }

    /**
     * @return array{null, null}|array{string, string}
     */
    private function getUnits(object $configuration): array
    {
        if (false === property_exists($configuration, 'unit')) {
            return [null, null];
        }

        $unit = $configuration->unit;

        if (\is_string($unit)) {
            return [$unit, $unit];
        }

        if (
            false === property_exists($unit, 'singular')
            || false === property_exists($unit, 'plural')
        ) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.check-reporter.%s.unit',
                    $this->reportName,
                    CheckReporterIntegration::GITHUB_STATUS->value,
                ),
                'must be a string or an object with "singular" and "plural" properties',
            );
        }

        $singular = $unit->singular;

        if (false === \is_string($singular)) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.check-reporter.%s.unit.singular',
                    $this->reportName,
                    CheckReporterIntegration::GITHUB_STATUS->value,
                ),
                'must be a string',
            );
        }

        $plural = $unit->plural;

        if (false === \is_string($plural)) {
            throw new AtPathException(
                sprintf(
                    '.reports.%s.check-reporter.%s.unit.plural',
                    $this->reportName,
                    CheckReporterIntegration::GITHUB_STATUS->value,
                ),
                'must be a string',
            );
        }

        return [$singular, $plural];
    }

    /**
     * @return array{StatusState, StatusState}
     */
    private function getStates(object $configuration): array
    {
        if (false === property_exists($configuration, 'states')) {
            return [StatusState::SUCCESS, StatusState::SUCCESS];
        }

        $states = $configuration->states;

        $path = sprintf(
            '.reports.%s.check-reporter.%s.states',
            $this->reportName,
            CheckReporterIntegration::GITHUB_STATUS->value,
        );
        $message = sprintf(
            'must be %s or an object with "on-lower" and "on-higher" properties',
            implode(
                ' or ',
                array_map(
                    static fn (string $key) => '"' . $key . '"',
                    array_keys(self::PREBUILD_STATES),
                ),
            ),
        );

        if (\is_string($states)) {
            if (\array_key_exists($states, self::PREBUILD_STATES)) {
                return self::PREBUILD_STATES[$states];
            }

            throw new AtPathException($path, $message);
        }

        if (false === \is_object($states)) {
            throw new AtPathException($path, $message);
        }

        $resolve = static function (string $property) use (
            $states,
            $path,
            $message
        ): StatusState {
            $properties = get_object_vars($states);

            if (false === \array_key_exists($property, $properties)) {
                throw new AtPathException($path, $message);
            }

            $state = $properties[$property];

            if (
                false === \is_string($state)
                || null === ($enum = StatusState::tryFrom($state))
            ) {
                throw new AtPathException(
                    $path . '.' . $property,
                    sprintf(
                        'must be %s',
                        implode(
                            ' or ',
                            array_map(
                                static fn (StatusState $enum) => '"' .
                                    $enum->value .
                                    '"',
                                StatusState::cases(),
                            ),
                        ),
                    ),
                );
            }

            return $enum;
        };

        return [$resolve('on-lower'), $resolve('on-higher')];
    }
}
