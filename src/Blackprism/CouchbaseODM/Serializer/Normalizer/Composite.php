<?php

namespace Blackprism\CouchbaseODM\Serializer\Normalizer;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\scalar;

/**
 * Composite
 * @TODO revoir le nom, composite est-ce la bonne traduction ?
 */
final class Composite implements NormalizerAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    /** @TODO utiliser la portée public */
    const CONFIG_OPTION                      = 'option';
    const CONFIG_OPTION_IDENTIFIER_GETTER    = 'identifierGetter';
    const CONFIG_OPTION_IDENTIFIER_GENERATOR = 'identifierGenerator';
    const CONFIG_MAPPING                     = 'mapping';
    const CONFIG_MAPPING_GETTER              = 'getter';
    const CONFIG_MAPPING_NORMALIZE           = 'normalize';
    const CONFIG_MAPPING_EXTERNAL            = 'external';
    const CONFIG_MAPPING_KEY_LINKED          = 'keyLinked';

    /**
     * @param array $context
     *
     * @throws \UnexpectedValueException
     */
    private function checkContext(array $context)
    {
        if (isset($context[self::CONFIG_MAPPING]) === false) {
            // @TODO utilisez une meilleure exception
            throw new \UnexpectedValueException('Missing context ' . self::CONFIG_MAPPING);
        }
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
        array $context): array {
        if (($mapping[$property][self::CONFIG_MAPPING_NORMALIZE] ?? false) === true) {
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

        $objectArray[$property] = $propertyValue;
        return [$objectArray, $normalized];
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
        $this->checkContext($context);
        $options = $context[self::CONFIG_OPTION] ?? [];
        $mapping = $context[self::CONFIG_MAPPING];

        $normalized = [];
        $objectArray  = [];
        $linkedProperties = [];
        if ($object instanceof NotifyPropertyChangedInterface && $object->isTracked() === true) {
            $properties = $object->getPropertiesChanged();

            if (isset($options[self::CONFIG_OPTION_IDENTIFIER_GETTER]) === true) {
                $objectId = $object->{$options[self::CONFIG_OPTION_IDENTIFIER_GETTER]}();
            }
        } else {
            $properties = $mapping;

            if (isset($options[self::CONFIG_OPTION_IDENTIFIER_GENERATOR]) === true) {
                $objectId = $options[self::CONFIG_OPTION_IDENTIFIER_GENERATOR]($object);
            }
        }

        foreach ($properties as $property => $values) {
            if (isset($mapping[$property][self::CONFIG_MAPPING_GETTER]) === false) {
                // @TODO utilisez une meilleure exception
                throw new \UnexpectedValueException('Missing argument ' . self::CONFIG_MAPPING_GETTER);
            }

            $propertyValue = $object->{$mapping[$property][self::CONFIG_MAPPING_GETTER]}();
            list($objectArray, $normalized) =
                $this->mapProperty($mapping, $property, $propertyValue, $objectArray, $normalized, $context);
        }

        $objectArray = array_replace($objectArray, $linkedProperties);

        if ($objectArray !== []) {
            if (isset($objectId) === true) {
                $normalized[$objectId] = $objectArray;
            } else {
                $normalized[] = $objectArray;
            }
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
