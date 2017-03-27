<?php

namespace Blackprism\CouchbaseODM\Repository;

use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionInterface;

/**
 * RepositoryFactory
 */
final class RepositoryFactory
{

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var object[]
     */
    private $repositories = [];

    /**
     * RepositoryFactory constructor.
     *
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param object $repository
     *
     * @return object
     */
    public function get($repository)
    {
        if (isset($this->repositories[get_class($repository)]) === true) {
            return $this->repositories[get_class($repository)];
        }

        if ($repository instanceof ConnectionAwareInterface) {
            $repository->connectionIs($this->connection);
        }

        $this->repositories[get_class($repository)] = $repository;

        return $repository;
    }
}
