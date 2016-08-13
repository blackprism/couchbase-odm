<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * Dsn
 *
 * @property string $dsn
 */
final class Dsn
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @param string $dsn
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $dsn)
    {
        if (strpos($dsn, 'couchbase://') === false) {
            throw new \InvalidArgumentException($dsn . ' should start with couchbase://');
        }

        $this->dsn = $dsn;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->dsn;
    }
}
