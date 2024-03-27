<?php

declare(strict_types=1);

namespace spec\K_pi\Data;

use Assert\Assert;
use K_pi\Data\Diff;
use PhpSpec\ObjectBehavior;

class DiffSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('test', 66.88, 66.85, 2);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Diff::class);
    }

    function it_has_a_diff()
    {
        $this->diff->shouldBe(-0.03);
    }

    function it_supports_data_provider()
    {
        foreach ($this->dataProvider() as $index => $data) {
            [
                'from'      => $from,
                'to'        => $to,
                'precision' => $precision,
                'diff'      => $diff,
                'changed'   => $changed
            ] = $data;

            $self = new Diff(
                name: 'test',
                from: $from,
                to: $to,
                precision: $precision,
            );

            $message = "Index #{$index}: " .
                'Value "%s" is not the same as expected value "%s".';

            Assert::that($self->diff)->same($diff, $message);
            Assert::that($self->changed)->same($changed, $message);
        }
    }

    private function dataProvider(): iterable
    {
        yield [
            'from'      => 66.88,
            'to'        => 66.85,
            'precision' => 2,
            'diff'      => -0.03,
            'changed'   => true,
        ];

        yield [
            'from'      => 66.85,
            'to'        => 66.88,
            'precision' => 2,
            'diff'      => 0.03,
            'changed'   => true,
        ];

        yield [
            'from'      => 66.88,
            'to'        => 66.85,
            'precision' => 1,
            'diff'      => 0,
            'changed'   => false,
        ];
    }
}
