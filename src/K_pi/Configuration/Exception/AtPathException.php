<?php

declare(strict_types=1);

namespace K_pi\Configuration\Exception;

use Exception;
use Throwable;

final class AtPathException extends Exception
{
    /**
     * @param non-empty-string $path
     */
    public function __construct(
        string $path,
        string $message,
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf('At path %s: %s', $path, $message),
            previous: $previous,
        );
    }

    /**
     * @param non-empty-string $path
     */
    public static function buildFromThrowable(
        string $path,
        Throwable $previous,
    ): self {
        return new self($path, $previous->getMessage(), $previous);
    }
}
