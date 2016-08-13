<?php

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * MergePaths
 */
class MergePaths implements DenormalizerAwareInterface, DenormalizerInterface
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
        $data = $this->merge($data);
        $data = $this->denormalizer->denormalize($data, $this->type, 'json');

        return $data;
    }

    /**
     * Merge composed keys like city.id in city, city.country.president into city and country
     *
     * @param array $values
     *
     * @return mixed
     */
    private function merge($values)
    {
        foreach ($values as $key => $value) {
            // Composed key
            $subKey = explode('.', $key, 2);

            if ($subKey !== [$key]) {
                if (isset($values[$subKey[0]][$subKey[1]]) === true
                    && is_array($values[$subKey[0]][$subKey[1]]) === true) {
                    $values[$subKey[0]][$subKey[1]] = array_replace($values[$subKey[0]][$subKey[1]], $values[$key]);
                } else {
                    $values[$subKey[0]][$subKey[1]] = $values[$key];
                }
                unset($values[$key]);

                if (strpos($subKey[1], '.') !== false) {
                    $values[$subKey[0]] = $this->merge($values[$subKey[0]]);
                }
            }
        }

        return $values;
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
