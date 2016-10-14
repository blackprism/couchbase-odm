<?php

namespace Blackprism\Demo\Repository\City\Configuration;

use Blackprism\CouchbaseODM\Serializer\Denormalizer\DispatchToType;
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

final class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface, PropertyChangedListenerAwareInterface
{
    use DenormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

    public function denormalize($rawCity, $class, $format = null, array $context = array())
    {
        $city = new Model\City();
        $city->setId($rawCity['id'] ?? '');
        $city->setName($rawCity['name'] ?? '');
        $city->setMayorId($rawCity['mayorId'] ?? '');

        if (isset($rawCity['country']) === true) {
            $country = $this->denormalizer->denormalize($rawCity['country'], 'country', $format, $context);
            $city->countryIs($country);
        }

        if (isset($rawCity['mayor']) === true) {
            $mayor = $this->denormalizer->denormalize($rawCity['mayor'], DispatchToType::class, $format, $context);
            $city->setMayor($mayor);
        }

        if ($city instanceof NotifyPropertyChangedInterface) {
            $city->track();
        }

        return $city;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type === 'city') {
            echo self::class . "\n";
            return true;
        } else {
            return false;
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
    public function previouslyDenormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($data['city']) === true) {
            $rawCity = $data['city'];
        } else {
            $rawCity = $data;
        }

        $city = new Model\City();
        $city->setId($rawCity['id'] ?? '');
        $city->setName($rawCity['name'] ?? '');
        $city->setMayorId($rawCity['mayorId'] ?? '');

        if (isset($rawCity['country']) === true) {
            /** @var Model\Country $country */
            $country = $this->denormalizer->denormalize(
                $rawCity['country'],
                Country\Configuration\Denormalizer::class,
                $format
            );
            $city->countryIs($country);
        }

        if (isset($rawCity['mayor']) === true) {
            /** @var Model\Mayor $mayor */
            $mayor = $this->denormalizer->denormalize(
                $rawCity['mayor'],
                Mayor\Configuration\Denormalizer::class,
                $format
            );
            $city->setMayor($mayor);
        }

        if ($city instanceof NotifyPropertyChangedInterface) {
            $city->track();
        }

        return $city;
    }

    public function previouSupportsDenormalization($data, $type, $format = null)
    {
        if ($type !== MergePaths::DENORMALIZATION_TYPE_OUTPUT) {
            return false;
        }

        if (isset($data['type']) === true && $data['type'] === 'city') {
            return true;
        }

        if (isset($data['city']['type']) === true && $data['city']['type'] === 'city') {
            return true;
        }

        return false;
    }
}

