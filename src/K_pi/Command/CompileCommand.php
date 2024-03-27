<?php

declare(strict_types=1);

namespace K_pi\Command;

use DateTimeImmutable;
use Exception;
use K_pi\Configuration\Extractor;
use K_pi\Storage;
use K_pi\ValueNormalizer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class CompileCommand extends AbstractCommand
{
    public function __construct(
        private Extractor $extractor,
        Storage\Integrations $storageIntegrations,
    ) {
        parent::__construct($storageIntegrations);
    }

    protected function configure(): void
    {
        $this->setName('compile')
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
        $now    = new DateTimeImmutable();

        foreach ($values as $name => $value) {
            $report->add(
                $name,
                $now,
                ValueNormalizer::normalize(
                    $value,
                    $reportConfiguration->getPrecision(),
                ),
            );
        }

        foreach ($reportConfiguration->getColors() as $name => $color) {
            $report->colorize($name, $color);
        }

        $storage->write($report, $reportConfiguration);

        return self::SUCCESS;
    }
}
