<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Blackprism\CouchbaseODM\Serializer\Normalizer\Composite;

class ShouldHaveConfigMappingGetter extends \FilterIterator
{
    /**
     * @var array
     */
    private $mapping;

    /**
     * @param array $mapping
     */
    public function mappingConfigIs(array $mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return bool
     */
    public function accept()
    {
        if (isset($mapping[$this->current()][Composite::CONFIG_MAPPING_GETTER]) === false) {
            // @TODO utilisez une meilleure exception
            throw new \UnexpectedValueException('Missing argument ' . Composite::CONFIG_MAPPING_GETTER);
        }

        return true;
    }
}
