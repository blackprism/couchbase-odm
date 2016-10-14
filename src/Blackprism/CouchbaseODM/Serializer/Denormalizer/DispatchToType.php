<?php

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * DispatchToType
 */
class DispatchToType implements DenormalizerAwareInterface, DenormalizerInterface
{

    use DenormalizerAwareTrait;

    /**
     * Type to use for output of denormalize.
     *
     * @var string
     */
    private $typeProperty = 'type';

    /**
     * Denormalizer to use for typeless.
     *
     * @var string
     */
    private $typelessDenormalizer = '';

    /**
     * @param string $type
     *
     * @return $this
     */
    public function typePropertyIs(string $type): self
    {
        $this->typeProperty = $type;

        return $this;
    }


    public function denormalizeTypelessWith(string $denormalizer): self
    {
        $this->typelessDenormalizer = $denormalizer;

        return $this;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    data to restore
     * @param string $class   the expected class to instantiate
     * @param string $format  format the given data was extracted from
     * @param array  $context options available to the denormalizer
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($data[$this->typeProperty]) === true) {
            $class = $data[$this->typeProperty];
        } else {
            $class = $this->typelessDenormalizer;
        }

        return $this->denormalizer->denormalize($data, $class, $format, $context);
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed  $data   Data to denormalize from
     * @param string $type   The class to which the data should be denormalized
     * @param string $format The format being deserialized from
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type === self::class) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
