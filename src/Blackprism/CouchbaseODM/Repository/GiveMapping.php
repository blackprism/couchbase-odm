<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Repository;

/**
 * Interface GiveMapping
 */
interface GiveMapping
{
    /**
     * @param MappingFactory $mappingFactory
     *
     * @return IsMapping
     */
    public function getMapping(MappingFactory $mappingFactory): IsMapping;
}
