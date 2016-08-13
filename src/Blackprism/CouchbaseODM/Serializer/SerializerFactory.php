<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerInterface;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayDecoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * SerializerFactory
 */
class SerializerFactory implements SerializerFactoryInterface, PropertyChangedListenerAwareInterface
{
    use PropertyChangedListenerAwareTrait;

    /**
     * @var SerializerInterface[]
     */
    private $serializers = [];

    /**
     * @param array $normalizers
     * @param array $encoders
     *
     * @return SerializerInterface
     */
    public function get(array $normalizers, array $encoders = []): SerializerInterface
    {
        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof PropertyChangedListenerAwareInterface) {
                $normalizer->propertyChangedListenerIs($this->propertyChangedListener);
            }
        }

        $normalizers[] = new Denormalizer\Collection();
        $normalizers[] = new Denormalizer\MergePaths();

        $encoders[] = new JsonEncoder();
        $encoders[] = new ArrayDecoder();

        $identifier = sha1(serialize($normalizers) . serialize($encoders));

        if (isset($this->serializers[$identifier]) === false) {
            $this->serializers[$identifier] =  new Serializer($normalizers, $encoders);
        }

        return $this->serializers[$identifier];
    }
}
