<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Normalizer;

use ArrayIterator;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Mapping
 */
class Mapping implements NormalizerAwareInterface, NormalizerInterface
{

    use NormalizerAwareTrait;

    /**
     * @var array
     */
    private $mappings;

    /**
     * Collection constructor.
     *
     * @param IsMapping[int] $mapping
     */
    public function __construct(IsMapping... $mapping)
    {
        $this->mappings = $mapping;
    }

    /**
     * @param array     $values
     * @param IsMapping $mapping
     *
     * @return object
     */
    private function mapValuesToObject(array $values, IsMapping $mapping)
    {
        $className = $mapping->getClass();
        $object = new $className;

        $valuesCanBeMapped = $this->getValuesCanBeMapped($values, $mapping);

        array_walk($valuesCanBeMapped, function ($value, $property) use ($mapping, $object) {
            if ($mapping->propertyHasMapping($property) === true) {
                $value = $this->mapValuesToObject($value, $mapping->getPropertyMapping($property));
            }

            $object->{$mapping->getPropertySetter($property)}($value);
        });

        $this->trackObjectIfNecessary($object);

        return $object;
    }

    private function getValuesCanBeMapped(array $values, IsMapping $mapping): array
    {
        return array_filter(
            $values,
            function ($property) use ($mapping) {
                return $mapping->hasProperty($property);
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * @param object $object
     */
    private function trackObjectIfNecessary($object)
    {
        if ($object instanceof NotifyPropertyChangedInterface) {
            $object->track();
        }
    }

    /**
     * @param object $object  Object to normalize
     * @param string $format  Format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = [];

        foreach ($this->mappings as $mapping) {
            if ($mapping->getClass() === get_class($object)) {
                $normalized = $this->normalizeObjectToArray($mapping, $object, $normalized);
            }
        }

        return $normalized;
    }

    /**
     * @param IsMapping $mapping
     * @param object    $object
     * @param array     $normalized
     *
     * @return array
     */
    public function normalizeObjectToArray(IsMapping $mapping, $object, array $normalized = [])
    {
        $propertyType = $mapping->getPropertyType();
        $normalized[$propertyType['property']] = $propertyType['value'];

        foreach ($mapping->getProperties() as $property => $parameters) {
            if (isset($parameters['mapping']) === true) {
                $subObject = $object->{$parameters['getter']}();

                if (is_object($subObject) === true) {
                    $normalized[$property] = $this->normalizeObjectToArray($parameters['mapping'], $subObject);
                }
            } else {
                $normalized[$property] = $object->{$parameters['getter']}();
            }
        }


        return $normalized;
    }

    /**
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        if (is_object($data) === false) {
            return false;
        }

        foreach ($this->mappings as $mapping) {
            if ($mapping->getClass() === get_class($data)) {
                return true;
            }
        }

        return false;
    }
}
