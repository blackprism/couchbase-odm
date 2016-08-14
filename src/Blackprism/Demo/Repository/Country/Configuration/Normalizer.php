<?php

namespace Blackprism\Demo\Repository\Country\Configuration;

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
     * @param object $country Country to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($country, $format = null, array $context = array())
    {
        $countryArray = [];
        $properties = $this->propertyChangedListener->getPropertiesChanged($country);

        foreach ($properties as $property) {
            if (isset($this->mapping[$property]) === true) {}
            $countryArray[$property] = $country->{$this->mapping[$property]}();
        }

        return $countryArray;
    }

    public function supportsNormalization($data, $format = null)
    {
        if (is_object($data) === true && get_class($data) === Model\Country::class) {
            return true;
        }

        return false;
    }
}

