<?php

declare(strict_types = 1);

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
    public function __construct(string $type = DispatchToType2::class)
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
        if (is_array($data) === false && $data instanceof \Traversable === false) {
            return new \EmptyIterator(); // @TODO how to log/inform about this error ?
        }

        list ($indexToExtract, $keyToExtract) = $this->extractIndexAndKey($class);

        if ($indexToExtract !== '') {
            return $this->denormalizeWithKeyToExtract($data[$indexToExtract], $format, $context, $keyToExtract);
        }

        reset($data);
        do {
            list ($index, $value) = each($data);
            $data[$index] = $this->denormalizeWithKeyToExtract($value, $format, $context, $keyToExtract);
        } while ($index !== null);

        return $data;
    }

    /**
     * @param mixed  $value
     * @param string $format
     * @param array  $context
     * @param string $keyToExtract
     *
     * @return mixed
     */
    private function denormalizeWithKeyToExtract(
        $value,
        string $format,
        array $context,
        string $keyToExtract
    ) {
        $value = $this->denormalizer->denormalize($value, $this->type, $format, $context);

        if ($keyToExtract !== '') {
            $value = $value[$keyToExtract];
        }

        return $value;
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
        if (strpos($type, 'collection[') === 0) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return string[]
     */
    private function extractIndexAndKey(string $type)
    {
        if ($type === 'collection[]') {
            return ['', ''];
        }

        preg_match('/^collection\[(?<index>[^]]*)\](\[(?<key>.+)\])?$/', $type, $match);

        return [$match['index'] ?? '', $match['key'] ?? ''];
    }
}
