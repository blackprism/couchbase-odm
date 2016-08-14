<?php

namespace Blackprism\Demo\Repository\Country\Configuration;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\Demo\Model\Country;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface, PropertyChangedListenerAwareInterface
{
    use DenormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $country = new Country();
        $country->setName($data['name']);

        if ($country instanceof NotifyPropertyChangedInterface) {
            $country->addPropertyChangedListener($this->propertyChangedListener);
        }


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
