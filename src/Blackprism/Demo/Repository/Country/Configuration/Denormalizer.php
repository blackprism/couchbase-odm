<?php

namespace Blackprism\Demo\Repository\Country\Configuration;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\Demo\Model\Country;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface
{
    use DenormalizerAwareTrait;

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
        $country = new Country();
        $country->setName($data['name']);

        if ($country instanceof NotifyPropertyChangedInterface) {
            $country->track();
        }

        return $country;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type === 'country') {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
