<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Blackprism\CouchbaseODM\Serializer\SerializerFactoryInterface;

/**
 * Interface SerializerFactoryAware
 */
interface SerializerFactoryAwareInterface
{
    /**
     * @param SerializerFactoryInterface $serializerFactory
     */
    public function serializerFactoryIs(SerializerFactoryInterface $serializerFactory);
}
