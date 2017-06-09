<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use ArrayIterator;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Repository\GiveMapping;
use Blackprism\CouchbaseODM\Repository\IsMapping;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * Mapping
 */
class Mapping implements DenormalizerAwareInterface, DenormalizerInterface
{

    use DenormalizerAwareTrait;

    /**
     * @var array
     */
    private $mappings;

    /**
     * Collection constructor.
     *
     * @param IsMapping[] $mapping
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

        $newData = new ArrayIterator();

        foreach ($data as $objectKeyName => $values) {
            foreach ($this->mappings as $mapping) {
                if ($objectKeyName === $mapping->getRoot()) {
                    $newData[$objectKeyName] = $this->mapValuesToObject($values, $mapping);
                }
            }
        }

        return $newData;
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
