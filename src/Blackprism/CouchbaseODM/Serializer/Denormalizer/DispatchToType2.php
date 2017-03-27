<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use ArrayIterator;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * DispatchToType
 */
class DispatchToType2 implements DenormalizerAwareInterface, DenormalizerInterface
{

    use DenormalizerAwareTrait;
    /**
     * Type to use for output of denormalize.
     *
     * @var string
     */
    private $typeProperty = 'type';

    /**
     * @var string
     */
    private $key = '';

    /**
     * @param string $type
     *
     * @return self
     */
    public function __construct(string $type = '')
    {
        if ($type !== '') {
            $this->typeProperty = $type;
        }
    }

    /**
     * @TODO revoir ce nom tout naze, un setter ne peut pas avoir le nom getX
     * @param string $key
     */
    public function getByKey(string $key)
    {
        $this->key = $key;
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
        if (is_array($data) === false) {
            return $data;
        }

        foreach ($data as $key => &$element) {
            $element = $this->denormalizer->denormalize(
                $element,
                $this->getClassForElement($element),
                $format,
                $context
            );
        }
        return $data;
    }

    /**
     * @param array $element
     *
     * @return string
     */
    private function getClassForElement(array $element): string
    {
        if (isset($element[$this->typeProperty]) === true) {
            return $element[$this->typeProperty];
        }

        // @TODO pas encore géré le typeless
        //$class = $this->typelessDenormalizer;
        var_dump("Pas trouvé de type", $this->typeProperty, $element);
        die;
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
