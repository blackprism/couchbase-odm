<?php

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Collection
 */
class Collection implements DenormalizerAwareInterface, DenormalizerInterface
{

    use DenormalizerAwareTrait;

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
    public function __construct($type = MergePaths::class)
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
        foreach ($data as &$value) {
            $value = $this->denormalizer->denormalize($value, $this->type, $format, $context);
        }

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
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
