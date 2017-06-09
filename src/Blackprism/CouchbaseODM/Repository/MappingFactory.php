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
     * @param string
     *
     * @return IsMapping
     */
    public function get(string $mappingClassName): IsMapping
    {
        if (isset($this->mappings[$mappingClassName]) === false) {
            $mappingFactory = new $mappingClassName;
            $this->mappings[$mappingClassName] = $mappingFactory->getMapping($this);
        }

        return $this->mappings[$mappingClassName];
    }
}
