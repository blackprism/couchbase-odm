<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * DispatchToType
 */
class DispatchToType implements DenormalizerAwareInterface, DenormalizerInterface
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
        foreach ($data as $key => &$element) {
            if (isset($context['key']) === true && $context['key'] !== $key) {
                continue;
            }

            if (isset($element[$this->typeProperty]) === true) {
                $class = $element[$this->typeProperty];
            } else {
                // @TODO pas encore géré le typeless
                //$class = $this->typelessDenormalizer;
                var_dump("Pas trouvé de type", $this->typeProperty, $element);
                die;
            }

            $element = $this->denormalizer->denormalize($element, $class, $format, $context);
        }

        return new \ArrayIterator($data);
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
