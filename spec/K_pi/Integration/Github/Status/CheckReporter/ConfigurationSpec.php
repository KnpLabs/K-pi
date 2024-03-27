<?php

declare(strict_types=1);

namespace spec\K_pi\Integration\Github\Status\CheckReporter;

use K_pi\Configuration\Exception\AtPathException;
use K_pi\Data\Github\StatusState;
use K_pi\Integration\Github\Status\CheckReporter\Configuration;
use PhpSpec\ObjectBehavior;
use stdClass;

final class ConfigurationSpec extends ObjectBehavior
{
    function let($configuration)
    {
        $this->beConstructedWith($configuration, 'report-name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Configuration::class);
    }

    function it_has_default_value()
    {
        $this->beConstructedWith(null, 'report-name');

        $this->onLower->shouldBe(StatusState::SUCCESS);
        $this->onHigher->shouldBe(StatusState::SUCCESS);
        $this->singularUnit->shouldBe(null);
        $this->pluralUnit->shouldBe(null);
    }

    function it_has_prebuild_states_higher_is_better($configuration)
    {
        $configuration->states = 'higher-is-better';

        $this->beConstructedWith($configuration, 'report-name');

        $this->onLower->shouldBe(StatusState::ERROR);
        $this->onHigher->shouldBe(StatusState::SUCCESS);
    }

    function it_has_prebuild_states_lower_is_better($configuration)
    {
        $configuration->states = 'lower-is-better';

        $this->beConstructedWith($configuration, 'report-name');

        $this->onLower->shouldBe(StatusState::SUCCESS);
        $this->onHigher->shouldBe(StatusState::ERROR);
    }

    function it_can_customize_states($configuration)
    {
        $onLower  = 'on-lower';
        $onHigher = 'on-higher';

        $configuration->states              = new stdClass();
        $configuration->states->{$onLower}  = StatusState::FAILURE->value;
        $configuration->states->{$onHigher} = StatusState::SUCCESS->value;

        $this->onLower->shouldBe(StatusState::FAILURE);
        $this->onHigher->shouldBe(StatusState::SUCCESS);
    }

    function it_has_default_states()
    {
        $this->onLower->shouldBe(StatusState::SUCCESS);
        $this->onHigher->shouldBe(StatusState::SUCCESS);
    }

    function it_has_a_simple_unit($configuration)
    {
        $configuration->unit = '%';

        $this->singularUnit->shouldBe('%');
        $this->pluralUnit->shouldBe('%');
    }

    function it_has_complexe_unit($configuration)
    {
        $configuration->unit           = new stdClass();
        $configuration->unit->singular = ' error';
        $configuration->unit->plural   = ' errors';

        $this->singularUnit->shouldBe(' error');
        $this->pluralUnit->shouldBe(' errors');
    }

    function it_has_default_unit()
    {
        $this->singularUnit->shouldBe(null);
        $this->pluralUnit->shouldBe(null);
    }

    function it_handle_bad_configuration($configuration)
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
            '.reports.report-name.check-reporter.github-status',
            'must be null or an object',
        ) => 1;

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.states',
            'must be "higher-is-better" or "lower-is-better" or an object with "on-lower" and "on-higher" properties',
        ) => [
            'states' => 'unknown',
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.states',
            'must be "higher-is-better" or "lower-is-better" or an object with "on-lower" and "on-higher" properties',
        ) => [
            'states' => null,
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.states',
            'must be "higher-is-better" or "lower-is-better" or an object with "on-lower" and "on-higher" properties',
        ) => [
            'states' => [
                'foo' => 'bar',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.states.on-lower',
            'must be "error" or "failure" or "pending" or "success"',
        ) => [
            'states' => [
                'on-lower'  => 'foo',
                'on-higher' => 'success',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.states.on-higher',
            'must be "error" or "failure" or "pending" or "success"',
        ) => [
            'states' => [
                'on-lower'  => 'success',
                'on-higher' => 'foo',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.unit',
            'must be a string or an object with "singular" and "plural" properties',
        ) => [
            'unit' => [
                'singular' => ' error',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.unit',
            'must be a string or an object with "singular" and "plural" properties',
        ) => [
            'unit' => [
                'plural' => ' errors',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.unit.singular',
            'must be a string',
        ) => [
            'unit' => [
                'singular' => null,
                'plural'   => ' errors',
            ],
        ];

        yield new AtPathException(
            '.reports.report-name.check-reporter.github-status.unit.plural',
            'must be a string',
        ) => [
            'unit' => [
                'singular' => ' error',
                'plural'   => null,
            ],
        ];
    }
}
