<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Repository;

use Blackprism\CouchbaseODM\Bucket\ProviderAware;
use Blackprism\CouchbaseODM\Bucket\Provider;

/**
 * RepositoryFactory
 */
final class RepositoryFactory
{

    /**
     * @var Provider
     */
    private $bucketPool;

    /**
     * @var MappingFactory
     */
    private $mappingFactory;

    /**
     * @var ProviderAware[]
     */
    private $repositories = [];

    /**
     * RepositoryFactory constructor.
     *
     * @param Provider       $pool
     * @param MappingFactory $mappingFactory
     */
    public function __construct(Provider $pool, MappingFactory $mappingFactory)
    {
        $this->bucketPool     = $pool;
        $this->mappingFactory = $mappingFactory;
    }

    /**
     * @param ProviderAware $repository
     *
     * @return ProviderAware
     */
    public function get(ProviderAware $repository)
    {
        if (isset($this->repositories[get_class($repository)]) === true) {
            return $this->repositories[get_class($repository)];
        }

        $repository->providerIs($this->bucketPool);

        if ($repository instanceof MappingFactoryAware) {
            $repository->mappingFactoryIs($this->mappingFactory);
        }

        $this->repositories[get_class($repository)] = $repository;

        return $repository;
    }
}
