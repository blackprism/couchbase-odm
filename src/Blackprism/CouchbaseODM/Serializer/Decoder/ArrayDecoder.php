<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Decoder;

use Symfony\Component\Serializer\Encoder\DecoderInterface;

/**
 * ArrayDecoder
 */
class ArrayDecoder implements DecoderInterface
{
    const FORMAT = 'array';

    /**
     * Decodes a string into PHP data.
     *
     * @param string $data    Data to decode
     * @param string $format  Format name
     * @param array  $context options that decoders have access to
     *
     * @return mixed
     */
    public function decode($data, $format, array $context = array())
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
    public function supportsDecoding($format)
    {
        if ($format === 'array') {
            return true;
        }

        return false;
    }
}
