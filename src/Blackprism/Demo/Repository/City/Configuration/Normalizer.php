<?php

namespace Blackprism\Demo\Repository\City\Configuration;

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
            'name' => [
                Composite::CONFIG_MAPPING_GETTER    => 'getName'
            ],
            'mayorId' => [
                Composite::CONFIG_MAPPING_GETTER    => 'getMayorId'
            ],
            'mayor' => [
                Composite::CONFIG_MAPPING_GETTER     => 'getMayor',
                Composite::CONFIG_MAPPING_KEY_LINKED => 'mayorId',
                Composite::CONFIG_MAPPING_NORMALIZE  => true,
                Composite::CONFIG_MAPPING_EXTERNAL   => true
            ],
            'country' => [
                Composite::CONFIG_MAPPING_GETTER    => 'getCountry',
                Composite::CONFIG_MAPPING_NORMALIZE => true
            ]
        ]
    ];

    public function __construct()
    {
        $this->context[Composite::CONFIG_OPTION] = [
            Composite::CONFIG_OPTION_IDENTIFIER_GETTER => 'getId',
            Composite::CONFIG_OPTION_IDENTIFIER_GENERATOR => function ($city) {
                return uniqid('city-');
            }
        ];
    }

    /**
     *
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
        if ($format === 'json' && is_object($data) === true && get_class($data) === Model\City::class) {
            echo self::class . "\n";
            return true;
        }

        return false;
    }
}

