<?php

declare(strict_types=1);

namespace spec\K_pi\Data;

use DateTimeImmutable;
use K_pi\Data\Extra;
use K_pi\Data\Report;
use PhpSpec\ObjectBehavior;

class ReportSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Report::class);
    }

    function it_is_able_to_store_data()
    {
        $moment1 = new DateTimeImmutable('1 day ago');
        $moment2 = new DateTimeImmutable();

        $this->add('foo', $moment2, 5);
        $this->add('foo', $moment1, 12);
        $this->add('bar', $moment1, 2);
        $this->add('baz', $moment2, 12);
        $this->add('baz', $moment1, -4);
        $this->add('bar', $moment2, 0);

        $this->shouldIterateLike([
            'foo' => [
                $moment1->format('Y-m-d') => 12,
                $moment2->format('Y-m-d') => 5,
            ],
            'bar' => [
                $moment1->format('Y-m-d') => 2,
                $moment2->format('Y-m-d') => 0,
            ],
            'baz' => [
                $moment1->format('Y-m-d') => -4,
                $moment2->format('Y-m-d') => 12,
            ],
        ]);
    }

    function it_unifies_data_with_consecutive_dates_but_same_value()
    {
        $moment0 = new DateTimeImmutable('2 day ago');
        $moment1 = new DateTimeImmutable('1 day ago');
        $moment2 = new DateTimeImmutable();

        $this->add('foo', $moment2, 5);
        $this->add('foo', $moment1, 12);
        $this->add('bar', $moment1, 2);
        $this->add('baz', $moment2, 12);
        $this->add('baz', $moment1, -4);
        $this->add('bar', $moment2, 0);
        $this->add('foo', $moment0, 12);

        $this->shouldIterateLike([
            'foo' => [
                $moment0->format('Y-m-d') => 12,
                $moment2->format('Y-m-d') => 5,
            ],
            'bar' => [
                $moment1->format('Y-m-d') => 2,
                $moment2->format('Y-m-d') => 0,
            ],
            'baz' => [
                $moment1->format('Y-m-d') => -4,
                $moment2->format('Y-m-d') => 12,
            ],
        ]);
    }

    function it_can_provide_last_value()
    {
        $moment1 = new DateTimeImmutable('1 day ago');
        $moment2 = new DateTimeImmutable();

        $this->add('foo', $moment2, 5);
        $this->add('foo', $moment1, 12);
        $this->add('bar', $moment1, 2);
        $this->add('baz', $moment2, 12);
        $this->add('baz', $moment1, -4);
        $this->add('bar', $moment2, 0);

        $this->last('foo')->shouldReturn(5);
        $this->last('bar')->shouldReturn(0);
        $this->last('baz')->shouldReturn(12);
        $this->last('tan')->shouldReturn(null);
    }

    function it_can_compile_total()
    {
        $moment0 = new DateTimeImmutable('2 day ago');
        $moment1 = new DateTimeImmutable('1 day ago');
        $moment2 = new DateTimeImmutable();

        $this->add('foo', $moment2, 5);
        $this->add('bar', $moment1, 2);
        $this->add('baz', $moment2, 12);
        $this->add('baz', $moment1, -4);
        $this->add('bar', $moment2, 0);
        $this->add('foo', $moment0, 12);

        $this->getExtra(Extra::TOTAL)->shouldReturn([
            $moment0->format('Y-m-d') => 12,
            $moment1->format('Y-m-d') => 12 + 2 - 4,
            $moment2->format('Y-m-d') => 5 + 12 + 0,
        ]);
    }
}
