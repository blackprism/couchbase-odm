<?php

namespace Blackprism\CouchbaseODM\Repository;

use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionInterface;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryAwareInterface;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryInterface;
use Blackprism\CouchbaseODM\Value\ClassName;

/**
 * RepositoryFactory
 */
class RepositoryFactory
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var SerializerFactoryInterface
     */
    private $serializerFactory;

    /**
     * @var object[]
     */
    private $repositories = [];

    /**
     * RepositoryFactory constructor.
     *
     * @param ConnectionInterface $connection
     * @param SerializerFactoryInterface $serializerFactory
     */
    public function __construct(ConnectionInterface $connection, SerializerFactoryInterface $serializerFactory)
    {
        $this->connection = $connection;
        $this->serializerFactory = $serializerFactory;
    }

    /**
     * @param ClassName $className
     *
     * @return object
     */
    public function get(ClassName $className)
    {
        if (isset($this->repositories[$className->value()]) === true) {
            return $this->repositories[$className->value()];
        }

        $fqcn = $className->value();
        $repository = new $fqcn();

        if ($repository instanceof ConnectionAwareInterface) {
            $repository->connectionIs($this->connection);
        }

        if ($repository instanceof SerializerFactoryAwareInterface) {
            $repository->serializerFactoryIs($this->serializerFactory);
        }

        $this->repositories[$className->value()] = $repository;

        return $repository;
    }
}
