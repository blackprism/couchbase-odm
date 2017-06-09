<?php

namespace Blackprism\CouchbaseODM\Repository;

/**
 * Interface MappingFactoryAware
 */
interface MappingFactoryAware
{
    /**
     * @param MappingFactory $mappingFactory
     */
    public function mappingFactoryIs(MappingFactory $mappingFactory);
}
