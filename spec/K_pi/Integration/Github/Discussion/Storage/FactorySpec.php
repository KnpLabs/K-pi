<?php

declare(strict_types=1);

namespace spec\K_pi\Integration\Github\Discussion\Storage;

use K_pi\Integration\Github;
use K_pi\Integration\Github\Discussion\Storage;
use K_pi\Integration\Github\Discussion\Storage\Configuration;
use K_pi\Integration\Github\Discussion\Storage\Factory;
use PhpSpec\ObjectBehavior;
use stdClass;

class FactorySpec extends ObjectBehavior
{
    function let(Github $github)
    {
        $this->beConstructedWith($github);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Factory::class);
    }

    function it_is_able_to_parse_configuration(Github $github)
    {
        $configuration      = new stdClass();
        $configuration->url = 'https://github.com/KnpLabs/K-pi/discussions/42';

        $this->build('report-name', $configuration)->shouldBeLike(
            new Storage(
                new Configuration($configuration, 'report-name'),
                $github->getWrappedObject(),
            ),
        );
    }
}
