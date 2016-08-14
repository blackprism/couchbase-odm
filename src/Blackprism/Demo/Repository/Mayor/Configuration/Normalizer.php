<?php

namespace Blackprism\Demo\Repository\Mayor\Configuration;

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
        'firstname' => 'getFirstname',
        'lastname' => 'getLastname'
    ];

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $mayor Mayor to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($mayor, $format = null, array $context = array())
    {
        $mayorArray = [];

        $properties = $this->propertyChangedListener->getPropertiesChanged($mayor);

        foreach ($properties as $property) {
            if (isset($this->mapping[$property]) === true) {}
            $mayorArray[$property] = $mayor->{$this->mapping[$property]}();
        }

        if ($mayorArray === []) {
            return [];
        }
/*
        if ($mayor->getId() === null) {
            $mayor->setId('mayor-' . uniqid());
        }*/

        return [$mayor->getId() => $mayorArray];
    }

    public function supportsNormalization($data, $format = null)
    {
        if (is_object($data) === true && get_class($data) === Model\Mayor::class) {
            return true;
        }

        return false;
    }
}

