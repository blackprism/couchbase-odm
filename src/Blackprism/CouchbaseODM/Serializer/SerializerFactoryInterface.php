<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Symfony\Component\Serializer\SerializerInterface;

/**
 * Interface SerializerFactoryInterface
 */
interface SerializerFactoryInterface
{
    /**
     * @param array $normalizers
     * @param array $encoders
     *
     * @return SerializerInterface
     */
    public function get(array $normalizers, array $encoders = []): SerializerInterface;
}
