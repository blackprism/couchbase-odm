<?php

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * FirstObject
 */
class FirstObject implements DenormalizerAwareInterface, DenormalizerInterface
{

    use DenormalizerAwareTrait;

    const DENORMALIZATION_TYPE_OUTPUT = self::class . '.output';

    /**
     * Type to use for output of denormalize.
     *
     * @var string
     */
    private $type;

    /**
     * Collection constructor.
     *
     * @param string $type type to use for output of denormalize
     */
    public function __construct($type = self::DENORMALIZATION_TYPE_OUTPUT)
    {
        $this->type = $type;
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
        $data = $this->denormalizer->denormalize(reset($data), $this->type, 'json');

        return $data;
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
            return true;
        }

        return false;
    }
}
