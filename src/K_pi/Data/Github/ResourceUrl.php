<?php

declare(strict_types=1);

namespace K_pi\Data\Github;

use InvalidArgumentException;

final class ResourceUrl
{
    /**
     * @var non-empty-string
     */
    public readonly string $owner;

    /**
     * @var non-empty-string
     */
    public readonly string $repository;

    /**
     * @var non-empty-string
     */
    public readonly string $type;

    /**
     * @var positive-int
     */
    public readonly int $number;

    /**
     * @throw InvalidArgumentException
     */
    public function __construct(string $url)
    {
        if (
            !preg_match(
                '#^https://github\.com/(.+)/(.+)/(\w+)/(\d+)$#',
                $url,
                $matches,
            )
        ) {
            throw new InvalidArgumentException('Invalid Github resource url.');
        }

        /**
         * @var non-empty-string $owner
         * @var non-empty-string $repository
         * @var non-empty-string $type
         * @var numeric-string   $number
         */
        [, $owner, $repository, $type, $number] = $matches;

        $this->owner      = $owner;
        $this->repository = $repository;
        $this->type       = $type;
        $number           = (int) $number;

        if (0 >= $number) {
            throw new InvalidArgumentException('Invalid Github resource url.');
        }

        $this->number = $number;
    }
}
