<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * DenormalizerChain
 */
final class DenormalizerChain implements DenormalizerInterface
{
    /**
     * @var DenormalizerInterface[int]
     */
    private $denormalizers;

    /**
     * @param DenormalizerInterface $denormalizer
     *
     * @return $this
     */
    public function chain(DenormalizerInterface $denormalizer)
    {
        $this->denormalizers[] = $denormalizer;

        return $this;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    Data to restore
     * @param string $class   The expected class to instantiate
     * @param string $format  Format the given data was extracted from
     * @param array  $context Options available to the denormalizer
     *
     * @return object
     *
     * @throws BadMethodCallException   Occurs when the normalizer is not called in an expected context
     * @throws InvalidArgumentException Occurs when the arguments are not coherent or not supported
     * @throws UnexpectedValueException Occurs when the item cannot be hydrated with the given data
     * @throws ExtraAttributesException Occurs when the item doesn't have attribute to receive given data
     * @throws LogicException           Occurs when the normalizer is not supposed to denormalize
     * @throws RuntimeException         Occurs if the class cannot be instantiated
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        /** @var DenormalizerInterface $denormalizer */
        foreach ($this->denormalizers as $denormalizer) {
            $data = $denormalizer->denormalize($data, $class, $format, $context);
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
        return true;
    }
}
