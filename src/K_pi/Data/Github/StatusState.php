<?php

declare(strict_types=1);

namespace K_pi\Data\Github;

enum StatusState: string
{
    case ERROR   = 'error';
    case FAILURE = 'failure';
    case PENDING = 'pending';
    case SUCCESS = 'success';
}
