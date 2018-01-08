<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use ArrayIterator;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Traversable;

/**
 * MergePaths
 */
class MergePaths implements DenormalizerInterface
{

    /**
     * @param array  $values
     * @param string $key
     *
     * @return array
     */
    private function mergeWhenKeyIsComposed(array $values, string $key): array
    {
        if (strpos($key, '.') !== false) {
            return $this->merge($values);
        }

        return $values;
    }

    /**
     * @param array  $values
     * @param string $key
     * @param array  $valuesToAppend
     *
     * @return array
     */
    private function appendOrReplaceForKey(array $values, string $key, array $valuesToAppend): array
    {
        if (isset($values[$key]) === true) {
            $values[$key] = array_replace($values[$key], $valuesToAppend);
            return $values;
        }

        $values[$key] = $valuesToAppend;

        return $values;
    }

    /**
     * Explode composed keys like city.id in city['id]', city.country.president into city['country']['president']
     *
     * @param array $values
     *
     * @return mixed
     */
    private function merge(array $values)
    {
        foreach ((new KeepComposedKey(new ArrayIterator($values))) as $key => $value) {
            list($mainKey, $subKey) = explode('.', $key, 2);

            $subValues = $this->mergeWhenKeyIsComposed([$subKey => $value], $subKey);
            $values = $this->appendOrReplaceForKey($values, $mainKey, $subValues);
            unset($values[$key]);
        }

        return $values;
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
        if (is_array($data) === true) {
            array_walk($data, function (&$item) {
                $item = $this->merge($item);
            });
        }

        if ($data instanceof Traversable) {
            iterator_apply($data, function (&$item) {
                $item = $this->merge($item);
            });
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
        if ($format === self::class) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
