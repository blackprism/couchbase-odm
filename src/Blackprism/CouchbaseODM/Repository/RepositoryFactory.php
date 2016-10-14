<?php

namespace Blackprism\CouchbaseODM\Repository;

use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryAwareInterface;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryInterface;
use Blackprism\CouchbaseODM\Value\ClassName;

/**
 * RepositoryFactory
 */
final class RepositoryFactory implements PropertyChangedListenerAwareInterface
{

    use PropertyChangedListenerAwareTrait;

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

        if ($repository instanceof PropertyChangedListenerAwareInterface) {
            $repository->propertyChangedListenerIs($this->propertyChangedListener);
        }

        $this->repositories[$className->value()] = $repository;

        return $repository;
    }
}
