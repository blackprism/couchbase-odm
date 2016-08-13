<?php

namespace Blackprism\Demo\Repository\Country\Configuration;

use Blackprism\Demo\Model\Country;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $country = new Country();
        $country->setName($data['name']);

        return $country;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type === self::class) {
            return true;
        }

        return false;
    }
}
