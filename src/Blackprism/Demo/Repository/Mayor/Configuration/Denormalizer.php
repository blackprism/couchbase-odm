<?php

namespace Blackprism\Demo\Repository\Mayor\Configuration;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\Demo\Model\Mayor;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class Denormalizer implements DenormalizerAwareInterface, DenormalizerInterface, PropertyChangedListenerAwareInterface
{
    use DenormalizerAwareTrait;
    use PropertyChangedListenerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = array())
    {
        $mayor = new Mayor();
        $mayor->setId($data['id']);
        $mayor->setFirstname($data['firstname']);
        $mayor->setLastname($data['lastname']);

        if ($mayor instanceof NotifyPropertyChangedInterface) {
            $mayor->track();
        }

        return $mayor;
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type === 'mayor') {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}
