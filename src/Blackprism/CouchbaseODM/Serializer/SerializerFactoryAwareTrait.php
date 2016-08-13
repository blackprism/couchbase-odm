<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Blackprism\CouchbaseODM\Serializer\SerializerFactoryInterface;

/**
 * Trait SerializerFactoryAwareTrait
 */
trait SerializerFactoryAwareTrait
{
    /**
     * @var SerializerFactoryInterface
     */
    private $serializerFactory;

    /**
     * @param SerializerFactoryInterface $serializerFactory
     */
    public function serializerFactoryIs(SerializerFactoryInterface $serializerFactory)
    {
        $this->serializerFactory = $serializerFactory;
    }
}
