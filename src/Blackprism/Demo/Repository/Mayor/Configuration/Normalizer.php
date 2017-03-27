<?php

namespace Blackprism\Demo\Repository\Mayor\Configuration;

use Blackprism\CouchbaseODM\Serializer\Normalizer\Composite;
use Blackprism\Demo\Model;
use Blackprism\Demo\Repository\Country;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class Normalizer implements NormalizerAwareInterface, NormalizerInterface
{
    use NormalizerAwareTrait;

    private $context = [
        Composite::CONFIG_MAPPING => [
            'firstname' => [
                Composite::CONFIG_MAPPING_GETTER => 'getFirstname'
            ],
            'lastname' => [
                Composite::CONFIG_MAPPING_GETTER => 'getLastname'
            ]
        ]
    ];

    public function __construct()
    {
        $this->context[Composite::CONFIG_OPTION] = [
            Composite::CONFIG_OPTION_IDENTIFIER_GETTER => 'getId',
            Composite::CONFIG_OPTION_IDENTIFIER_GENERATOR => function ($city) {
                return uniqid('mayor-');
            }
        ];
    }

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
        return $this->normalizer->normalize($object, Composite::class, $this->context);
    }

    public function supportsNormalization($data, $format = null)
    {
        if ($format === null && is_object($data) === true && get_class($data) === Model\Mayor::class) {
            return true;
        }

        return false;
    }
}

