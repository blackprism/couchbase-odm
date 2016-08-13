<?php

namespace Blackprism\Demo\Repository\City\Configuration;

use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer\MergePaths;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface, PropertyChangedListenerAwareInterface
{
    use DenormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

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
        $city = new Model\City();
        $city->setId($data['city']['id']);
        $city->setName($data['city']['name']);
        $city->setMayorId($data['city']['mayorId']);

        if (isset($data['city']['country']) === true) {
            /** @var Model\Country $country */
            $country = $this->denormalizer->denormalize($data['city']['country'], Country\Configuration\Denormalizer::class, $format);
            $city->countryIs($country);
        }

        if (isset($data['city']['mayor']) === true) {
            /** @var Model\Mayor $mayor */
            $mayor = $this->denormalizer->denormalize($data['city']['mayor'], Mayor\Configuration\Denormalizer::class, $format);
            $city->setMayor($mayor);
        }

        if ($city instanceof NotifyPropertyChangedInterface) {
            $city->addPropertyChangedListener($this->propertyChangedListener);
        }

        return $city;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type !== MergePaths::DENORMALIZATION_TYPE_OUTPUT) {
            return false;
        }

        if (isset($data['city']['type']) === true && $data['city']['type'] === 'city') {
            return true;
        }

        return false;
    }
}

