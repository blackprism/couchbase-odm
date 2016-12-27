<?php

namespace Blackprism\CouchbaseODM\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * Collection
 */
class Collection implements NormalizerAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $values  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($values, $format = null, array $context = [])
    {
        if ($values instanceof \Iterator === false) {
            return [];
        }

        if ($this->normalizer === false) {
            return [];
        }

        foreach ($values as &$value) {
            $value = $this->normalizer->normalize($value, $format, $context);
        }

        echo "Bouh\n";
        var_dump($values);
        die;

        return $values;
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if ($format === self::class) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
