<?php

declare(strict_types=1);

namespace K_pi\Configuration\Extractor;

use K_pi\Configuration;
use K_pi\Configuration\Extractor;
use Symfony\Component\Console\Input\InputInterface;

final class StrategyExtractor implements Extractor
{
    /**
     * @var array<Extractor>
     */
    private readonly array $extractors;

    public function __construct(YamlFileExtractor $yamlFileExtractor)
    {
        $this->extractors = [$yamlFileExtractor];
    }

    public function extract(InputInterface $input): ?Configuration
    {
        foreach ($this->extractors as $extractor) {
            if (null !== ($configuration = $extractor->extract($input))) {
                return $configuration;
            }
        }

        return null;
    }
}
