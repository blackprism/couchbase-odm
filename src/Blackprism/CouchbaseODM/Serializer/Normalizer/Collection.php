<?php

namespace Blackprism\CouchbaseODM\Serializer\Normalizer;

use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Collection
 */
class Collection implements NormalizerAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $iterable Iterable object to normalize
     * @param string $format   format the normalization result will be encoded as
     * @param array  $context  Context options for the normalizer
     *
     * @throws InvalidArgumentException
     * @throws CircularReferenceException
     * @throws LogicException
     *
     * @return array|scalar
     */
    public function normalize($iterable, $format = null, array $context = [])
    {
        if (is_iterable($iterable) === false) {
            return [];
        }

        if ($this->normalizer === false) {
            return [];
        }

        $normalized = [];

        foreach ($iterable as $key => $value) {
            $normalized[$key] = $this->normalizer->normalize($value, $format, $context);
        }

        return $normalized;
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
        if (is_iterable($data) === true) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
