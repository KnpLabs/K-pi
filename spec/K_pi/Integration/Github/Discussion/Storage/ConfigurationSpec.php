<?php

declare(strict_types=1);

namespace spec\K_pi\Integration\Github\Discussion\Storage;

use K_pi\Configuration\Exception\AtPathException;
use K_pi\Integration\Github\Discussion\Storage\Configuration;
use PhpSpec\ObjectBehavior;

class ConfigurationSpec extends ObjectBehavior
{
    function let($configuration)
    {
        $configuration->url = 'https://github.com/KnpLabs/K-pi/discussions/42';

        $this->beConstructedWith($configuration, 'report-name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    function it_is_able_to_load_minimum_configuration()
    {
        $this->discussion->owner->shouldBe('KnpLabs');
        $this->discussion->repository->shouldBe('K-pi');
        $this->discussion->number->shouldBe(42);
        $this->report->shouldBe(true);
        $this->persist->shouldBe(true);
    }

    function it_supports_full_configuration($configuration)
    {
        $configuration->report  = false;
        $configuration->persist = false;

        $this->discussion->owner->shouldBe('KnpLabs');
        $this->discussion->repository->shouldBe('K-pi');
        $this->discussion->number->shouldBe(42);
        $this->report->shouldBe(false);
        $this->persist->shouldBe(false);
    }

    function it_handle_bad_configuration()
    {
        foreach ($this->invalidDataProvider() as $exception => $configuration) {
            $configuration = json_decode(json_encode($configuration), false);

            $this->beConstructedWith($configuration, 'report-name');

            $this->shouldThrow($exception)->duringInstantiation();
        }
    }

    private function invalidDataProvider()
    {
        yield new AtPathException(
            '.reports.report-name.storage.github-discussion.url',
            'Invalid Github resource url.',
        ) => [
            'url'     => 'https://github.com/KnpLabs/K-pi/discussions/0',
            'persist' => true,
            'report'  => true,
        ];

        yield new AtPathException(
            '.reports.report-name.storage.github-discussion.url',
            'Invalid Github resource url.',
        ) => [
            'url'     => 'https://github.com/KnpLabs//discussions/42',
            'persist' => true,
            'report'  => true,
        ];

        yield new AtPathException(
            '.reports.report-name.storage.github-discussion.persist',
            'must be a boolean.',
        ) => [
            'url'     => 'https://github.com/KnpLabs/K-pi/discussions/42',
            'persist' => 1,
            'report'  => true,
        ];
    }
}
