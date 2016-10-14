<?php

namespace Blackprism\Demo\Repository\City\Configuration;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Serializer\Denormalizer\DispatchToType;
use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param mixed $rawCity
     * @param string $class
     * @param null $format
     * @param array $context
     *
     * @return Model\City
     */
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
}

