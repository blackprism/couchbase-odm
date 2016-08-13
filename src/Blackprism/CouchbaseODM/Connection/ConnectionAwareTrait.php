<?php

namespace Blackprism\CouchbaseODM\Connection;

/**
 * Trait ConnectionAwareTrait
 */
trait ConnectionAwareTrait
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function connectionIs(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }
}
