<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Encoder;

use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

/**
 * ArrayEncoder
 */
class ArrayEncoder implements EncoderInterface
{
    const FORMAT = 'array';

    /**
     * Encodes data into the given format.
     *
     * @param mixed  $data    Data to encode
     * @param string $format  Format name
     * @param array  $context options that normalizers/encoders have access to
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     */
    public function encode($data, $format, array $context = array())
    {
        return $data;
    }

    /**
     * Checks whether the deserializer can decode from given format.
     *
     * @param string $format format name
     *
     * @return bool
     */
    public function supportsEncoding($format)
    {
        if (self::FORMAT === $format) {
            return true;
        }

        return false;
    }
}
