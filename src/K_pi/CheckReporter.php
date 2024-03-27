<?php

declare(strict_types=1);

namespace K_pi;

use K_pi\Data\Diff;

interface CheckReporter
{
    public function send(Diff ...$notifications): void;
}
