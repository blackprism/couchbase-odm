<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

/**
 * Serializer
 */
final class Serializer implements SerializerInterface, PropertyChangedListenerAwareInterface
{

    use PropertyChangedListenerAwareTrait;

    /**
     * @var array
     */
    private $normalizers;

    /**
     * @var array
     */
    private $encoders;

    /**
     * @var string
     */
    private $type = Denormalizer\DispatchToManyTypes::class;

    /**
     * Serializer constructor.
     *
     * @param array $normalizers
     * @param array $encoders
     */
    public function __construct(array $normalizers = [], array $encoders = [])
    {
        $this->normalizers = $normalizers;
        $this->encoders = $encoders;
    }

    /**
     * @param string $type
     */
    public function typeIs(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return SerializerInterface
     */
    private function getSerializer()
    {
        // @TODO faut-il vraiment une nouvelle instance pour chaque demande de serializer ?
        $normalizers = [];
        $normalizers[Denormalizer\CollectionFirstObject::class] = new Denormalizer\CollectionFirstObject();
        $normalizers[Denormalizer\Collection::class]            = new Denormalizer\Collection();
        $normalizers[Denormalizer\MergePaths::class]            = new Denormalizer\MergePaths($this->type);
        $normalizers[Denormalizer\DispatchToManyTypes::class]   = new Denormalizer\DispatchToManyTypes();
        $normalizers[Denormalizer\DispatchToType::class]        = new Denormalizer\DispatchToType();
        $normalizers[Denormalizer\FirstObject::class]           = new Denormalizer\FirstObject();
        $normalizers[Denormalizer\Raw::class]                   = new Denormalizer\Raw();
        $normalizers[Normalizer\Collection::class]              = new Normalizer\Collection();
        $normalizers[Normalizer\Composite::class]               = new Normalizer\Composite();
        $normalizers = array_replace($normalizers, $this->setPropertyChangedListener($this->normalizers));

        $encoders = [];
        $encoders[JsonEncoder::class]  = new JsonEncoder();
        $encoders[ArrayDecoder::class] = new ArrayDecoder();
        $encoders[ArrayEncoder::class] = new ArrayEncoder();
        $encoders = array_replace($encoders, $this->encoders);

        return new SymfonySerializer($normalizers, $encoders);
    }

    /**
     * @param array $normalizers
     *
     * @return array
     */
    private function setPropertyChangedListener(array $normalizers)
    {
        return $normalizers;
        if ($this->propertyChangedListener !== null) {
            foreach ($normalizers as $normalizer) {
                if ($normalizer instanceof PropertyChangedListenerAwareInterface) {
                    $normalizer->propertyChangedListenerIs($this->propertyChangedListener);
                }
            }
        }

        return $normalizers;
    }

    /**
     * Serializes data in the appropriate format.
     *
     * @param mixed  $data    any data
     * @param string $format  format name
     * @param array  $context options normalizers/encoders have access to
     *
     * @return string
     */
    public function serialize($data, $format, array $context = [])
    {
        return $this->getSerializer()->serialize($data, $format, $context);
    }

    /**
     * Deserializes data into the given type.
     *
     * @param mixed  $data
     * @param string $type
     * @param string $format
     * @param array  $context
     *
     * @return object
     */
    public function deserialize($data, $type, $format, array $context = [])
    {
        return $this->getSerializer()->deserialize($data, $type, $format, $context);
    }
}
