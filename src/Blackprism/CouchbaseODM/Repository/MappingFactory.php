<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Repository;

/**
 * MappingFactory
 */
final class MappingFactory
{
    /**
     * @var IsMapping[]
     */
    private $mappings = [];

    /**
     * @param GiveMapping $mapping
     *
     * @return IsMapping
     */
    public function get(GiveMapping $mapping): IsMapping
    {
        $mappingIdentifier = get_class($mapping);
        if (isset($this->mappings[$mappingIdentifier]) === false) {
            $this->mappings[$mappingIdentifier] = $mapping->getMapping($this);
        }

        return $this->mappings[$mappingIdentifier];
    }
}
