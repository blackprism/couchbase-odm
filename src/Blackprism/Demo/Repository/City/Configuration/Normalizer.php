<?php

namespace Blackprism\Demo\Repository\City\Configuration;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Normalizer implements NormalizerAwareInterface, NormalizerInterface, PropertyChangedListenerAwareInterface
{
    use NormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

    private $mapping = [
        'name' => 'getName',
        'mayorId' => 'getMayorId',
        'country' => 'getCountry'
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
        $city = $object;
        $cityArray = [];

        $properties = $this->propertyChangedListener->getPropertiesChanged($object);

        foreach ($properties as $property) {
            if (isset($this->mapping[$property]) === true) {}
            $cityArray[$property] = $city->{$this->mapping[$property]}();
        }

        $cityArray['country'] = $this->normalizer->normalize($city->getCountry(), $format);
        $mayorFinal = $this->normalizer->normalize($city->getMayor(), $format);

        if ($cityArray !== []) {
            $cityFinal = [
                $city->getId() => $cityArray
            ];
        } else {
            $cityFinal = $cityArray;
        }

        return array_merge($cityFinal, $mayorFinal);
    }

    public function supportsNormalization($data, $format = null)
    {
        if (is_object($data) === true && get_class($data) === Model\City::class
            || is_array($data) === true && get_class($data['city']) === Model\City::class) {
            return true;
        }

        return false;
    }
}

