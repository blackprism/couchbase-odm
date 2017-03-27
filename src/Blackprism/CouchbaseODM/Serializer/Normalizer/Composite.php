<?php

namespace Blackprism\CouchbaseODM\Serializer\Normalizer;

use ArrayIterator;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Serializer\Denormalizer\ShouldHaveConfigMappingGetter;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Composite
 * @TODO revoir le nom, composite est-ce la bonne traduction ?
 */
final class Composite implements NormalizerAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    public const CONFIG_OPTION                      = 'option';
    public const CONFIG_OPTION_IDENTIFIER_GETTER    = 'identifierGetter';
    public const CONFIG_OPTION_IDENTIFIER_GENERATOR = 'identifierGenerator';
    public const CONFIG_MAPPING                     = 'mapping';
    public const CONFIG_MAPPING_GETTER              = 'getter';
    public const CONFIG_MAPPING_NORMALIZE           = 'normalize';
    public const CONFIG_MAPPING_EXTERNAL            = 'external';
    public const CONFIG_MAPPING_KEY_LINKED          = 'keyLinked';

    /**
     * @param array $context
     *
     * @throws \UnexpectedValueException
     */
    private function getOptionsAndMappingFromContext(array $context)
    {
        if (isset($context[self::CONFIG_MAPPING]) === false) {
            // @TODO utilisez une meilleure exception
            throw new \UnexpectedValueException('Missing context ' . self::CONFIG_MAPPING);
        }

        return [
            $context[self::CONFIG_OPTION] ?? [],
            $context[self::CONFIG_MAPPING]
        ];
    }

    /**
     * @param array  $mapping
     * @param string $property
     * @param mixed  $propertyValue
     * @param array  $objectArray
     * @param array  $normalized
     * @param array  $context
     *
     * @return array
     */
    private function mapProperty(
        array $mapping,
        string $property,
        $propertyValue,
        array $objectArray,
        array $normalized,
        array $context
    ): array {
        if (($mapping[$property][self::CONFIG_MAPPING_NORMALIZE] ?? false) === false) {
            $objectArray[$property] = $propertyValue;
            return [$objectArray, $normalized];
        }

        $propertyNormalized = $this->normalizer->normalize($propertyValue, null, $context);

        if (($mapping[$property][self::CONFIG_MAPPING_KEY_LINKED] ?? false) !== false) {
            $linkedProperties[$mapping[$property][self::CONFIG_MAPPING_KEY_LINKED]] = key($propertyNormalized);
        }

        if (($mapping[$property][self::CONFIG_MAPPING_EXTERNAL] ?? false) === true) {
            $normalized = array_replace($normalized, $propertyNormalized);
            return [$objectArray, $normalized];
        }

        $objectArray[$property] = $propertyNormalized;
        return [$objectArray, $normalized];
    }

    /**
     * @param object $object
     *
     * @return bool
     */
    private function isObjectIsTracked($object): bool
    {
        return $object instanceof NotifyPropertyChangedInterface && $object->isTracked();
    }

    /**
     * @param object $object
     *
     * @return array|null
     */
    private function getPropertiesForObject($object): ?array
    {
        if ($this->isObjectIsTracked($object) === true) {
            return $object->getPropertiesChanged();
        }

        return null;
    }

    /**
     * @param object $object
     *
     * @return array|null
     */
    private function getObjectIdForObject($object): ?array
    {
        $objectIsTracked = $this->isObjectIsTracked($object);

        if ($objectIsTracked === true && isset($options[self::CONFIG_OPTION_IDENTIFIER_GETTER]) === true) {
            return $object->{$options[self::CONFIG_OPTION_IDENTIFIER_GETTER]}();
        }

        if ($objectIsTracked === false && isset($options[self::CONFIG_OPTION_IDENTIFIER_GENERATOR]) === true) {
            return $options[self::CONFIG_OPTION_IDENTIFIER_GENERATOR]($object);
        }

        return null;
    }

    /**
     * @param object $object
     * @param array  $mapping
     *
     * @return array
     */
    private function normalizeObject($object, array $mapping, array $context = []): array
    {
        $normalized = [];
        $objectArray = [];

        $propertiesWithConfigMappingGetter =
            new ShouldHaveConfigMappingGetter(new ArrayIterator($this->getPropertiesForObject($object) ?? $mapping));
        $propertiesWithConfigMappingGetter->mappingConfigIs($mapping);

        foreach ($propertiesWithConfigMappingGetter as $property => $values) {
            $propertyValue = $object->{$mapping[$property][self::CONFIG_MAPPING_GETTER]}();
            list($objectArray, $normalized) =
                $this->mapProperty($mapping, $property, $propertyValue, $objectArray, $normalized, $context);
        }

        return $objectArray;
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     *
     * @throws \UnexpectedValueException
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalized = [];
        list ($options, $mapping) = $this->getOptionsAndMappingFromContext($context);

        $objectArray = $this->normalizeObject($object, $mapping, $context);

        $objectId = $this->getObjectIdForObject($object);
        if ($objectArray !== [] && isset($objectId) === true) {
            $normalized[$objectId] = $objectArray;
            return $normalized;
        }

        if ($objectArray !== []) {
            $normalized[] = $objectArray;
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
        if ($format === self::class) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
