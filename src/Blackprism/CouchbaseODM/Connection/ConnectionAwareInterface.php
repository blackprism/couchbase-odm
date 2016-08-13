<?php

namespace Blackprism\CouchbaseODM\Connection;

/**
 * Interface ConnectionAwareInterface
 */
interface ConnectionAwareInterface
{
    /**
     * @param ConnectionInterface $connection
     */
    public function connectionIs(ConnectionInterface $connection);
}
