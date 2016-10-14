<?php

namespace Blackprism\Demo\Repository\Country\Configuration;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Normalizer\Composite;
use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Normalizer implements NormalizerAwareInterface, NormalizerInterface, PropertyChangedListenerAwareInterface
{
    use NormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

    private $context = [
        Composite::CONFIG_MAPPING => [
            'name' => [
                Composite::CONFIG_MAPPING_GETTER    => 'getName',
                Composite::CONFIG_MAPPING_NORMALIZE => false
            ]
        ]
    ];

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = array())
    {
        $normalized = $this->normalizer->normalize($object, Composite::class, $this->context);
        return reset($normalized);
    }

    public function supportsNormalization($data, $format = null)
    {
        if (is_object($data) === true && get_class($data) === Model\Country::class) {
            return true;
        }

        return false;
    }
}

