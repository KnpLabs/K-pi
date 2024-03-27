<?php

declare(strict_types=1);

namespace spec\K_pi\Data\Github;

use InvalidArgumentException;
use K_pi\Data\Github\ResourceUrl;
use PhpSpec\ObjectBehavior;

class ResourceUrlSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('https://github.com/KnpLabs/K-pi/pull/11');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourceUrl::class);
    }

    function it_can_get_data_from_an_url()
    {
        $this->owner->shouldBe('KnpLabs');
        $this->repository->shouldBe('K-pi');
        $this->type->shouldBe('pull');
        $this->number->shouldBe(11);
    }

    function it_detects_bad_resource_urls()
    {
        foreach ($this->failureDataProvider() as $url => $message) {
            $this->beConstructedWith($url);

            $this->shouldThrow(
                new InvalidArgumentException($message),
            )->duringInstantiation();
        }
    }

    private function failureDataProvider(): iterable
    {
        yield 'https://github.com//K-pi/pull/11' => 'Invalid Github resource url.';

        yield 'https://github.com/KnpLabs//pull/11' => 'Invalid Github resource url.';

        yield 'https://github.com/KnpLabs/K-pi//11' => 'Invalid Github resource url.';

        yield 'https://github.com/KnpLabs/K-pi/pull/0' => 'Invalid Github resource url.';

        yield 'https://github.com/KnpLabs/K-pi/pull/-1' => 'Invalid Github resource url.';

        yield 'https://gitlab.org/KnpLabs/K-pi/pull/11' => 'Invalid Github resource url.';
    }
}
