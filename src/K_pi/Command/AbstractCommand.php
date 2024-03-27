<?php

declare(strict_types=1);

namespace K_pi\Command;

use Assert\Assert;
use Exception;
use K_pi\Data\StorageIntegration;
use K_pi\Storage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Yaml\Yaml;

abstract class AbstractCommand extends Command
{
    public function __construct(
        private readonly Storage\Integrations $storageIntegrations,
    ) {
        parent::__construct();
    }

    /**
     * @return array<non-empty-string, float|int>
     */
    protected function getValues(InputInterface $input): array
    {
        $values = Yaml::parse($this->readArgument($input, 'values'));

        Assert::that($values)->isArray();
        Assert::that(array_keys($values))->all()->string()->notEmpty();

        return array_map(static function ($value): float|int {
            if (\is_int($value) || \is_float($value)) {
                return $value;
            }

            if (\is_string($value) && is_numeric($value)) {
                return (float) $value;
            }

            throw new Exception(
                sprintf(
                    '%s is not an integer, a float or a numeric string.',
                    match (\gettype($value)) {
                        'string'  => sprintf('"%s"', addslashes($value)),
                        'boolean' => $value ? 'true' : 'false',
                        default   => \gettype($value),
                    },
                ),
            );
        }, $values);
    }

    /**
     * @return non-empty-string
     */
    protected function readArgument(
        InputInterface $input,
        string $argumentName,
    ): string {
        $argument = $input->getArgument($argumentName);

        Assert::that($argument)->string()->notEmpty();

        /**
         * @var non-empty-string
         */
        return $argument;
    }

    /**
     * @param non-empty-string $reportName
     */
    protected function getStorage(
        string $reportName,
        StorageIntegration $integration,
        mixed $configuration,
    ): Storage {
        return $this->storageIntegrations
            ->get($integration)
            ->build($reportName, $configuration)
        ;
    }
}
