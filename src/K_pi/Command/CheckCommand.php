<?php

declare(strict_types=1);

namespace K_pi\Command;

use Exception;
use K_pi\CheckReporter;
use K_pi\Configuration\Extractor;
use K_pi\Data\Diff;
use K_pi\Storage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CheckCommand extends AbstractCommand
{
    public function __construct(
        private readonly CheckReporter\Integrations $checkReporterIntegrations,
        private readonly Extractor $extractor,
        Storage\Integrations $storageIntegrations,
    ) {
        parent::__construct($storageIntegrations);
    }

    protected function configure(): void
    {
        $this->setName('check')
            ->addArgument('report-name', InputArgument::REQUIRED)
            ->addArgument('values', InputArgument::REQUIRED)
            ->addOption(
                'configuration-file',
                mode: InputOption::VALUE_OPTIONAL,
            )
        ;
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $reportName    = $this->readArgument($input, 'report-name');
        $configuration = $this->extractor->extract($input);

        if (null === $configuration) {
            throw new Exception(
                'Unable to read configuration, no configuration file found.',
            );
        }

        $reportConfiguration = $configuration->get($reportName);
        $storage             = $this->getStorage(
            $reportName,
            ...$reportConfiguration->getStorageConfiguration(),
        );
        $report = $storage->read();
        $values = $this->getValues($input);

        $notifications = array_map(
            static fn (float|int $value, string $name): Diff => new Diff(
                name: $name,
                from: $report->last($name) ?? 0,
                to: $value,
                precision: $reportConfiguration->getPrecision(),
            ),
            array_values($values),
            array_keys($values),
        );

        if ([] === $notifications) {
            return self::SUCCESS;
        }

        foreach (
            $reportConfiguration->getCheckReportersConfiguration() as $checkReporterIntegration => $checkReporterConfiguration
        ) {
            $this->checkReporterIntegrations
                ->get($checkReporterIntegration)
                ->build($checkReporterConfiguration, $reportName)
                ->send(...$notifications)
            ;
        }

        return self::SUCCESS;
    }
}
