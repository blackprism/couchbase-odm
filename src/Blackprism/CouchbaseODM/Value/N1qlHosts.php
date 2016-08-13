<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * Dsn
 *
 * @property string $dsn
 */
final class N1qlHosts
{
    /**
     * @var string
     */
    private $n1qlHosts;

    /**
     * @param string[] $hosts
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $hosts)
    {
        foreach ($hosts as $host) {
            $host = (string) $host;
            if (strpos($host, 'http://') === false) {
                throw new \InvalidArgumentException($host . ' should start with http://');
            }

            $this->n1qlHosts[] = $host;
        }
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->n1qlHosts;
    }
}
