<?php

namespace Blackprism\CouchbaseODM\Serializer;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayDecoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * SerializerFactory
 */
class SerializerFactory implements SerializerFactoryInterface, PropertyChangedListenerAwareInterface
{
    use PropertyChangedListenerAwareTrait;

    /**
     * @var SerializerInterface[]
     */
    private $serializers = [];

    /**
     * @param array $normalizers
     * @param array $encoders
     *
     * @return SerializerInterface
     */
    public function get(array $normalizers, array $encoders = []): SerializerInterface
    {
        // @TODO est-ce que c'est assez performant ce sha1 et les 2 serialize ?
        $identifier = sha1(serialize($normalizers) . serialize($encoders));

        // @TODO est-ce que je gère ça moi même ou pas ? Ou je retourne toujours une nouvelle instance ?
        if (isset($this->serializers[$identifier]) === true) {
            return $this->serializers[$identifier];
        }

        $encoders[] = new JsonEncoder();
        $encoders[] = new ArrayDecoder();

        $this->serializers[$identifier] = new Serializer($this->initNormalizers($normalizers), $encoders);

        return $this->serializers[$identifier];
    }

    /**
     * @param array $normalizers
     *
     * @return array
     */
    private function initNormalizers(array $normalizers)
    {
        $collectionFound = false;
        $mergePathsFound = false;

        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof PropertyChangedListenerAwareInterface) {
                $normalizer->propertyChangedListenerIs($this->propertyChangedListener);
            }

            if ($normalizer instanceof Denormalizer\Collection) {
                $collectionFound = true;
            }

            if ($normalizer instanceof Denormalizer\MergePaths) {
                $mergePathsFound = true;
            }
        }

        if ($collectionFound === false) {
            $normalizers[] = new Denormalizer\Collection();
        }

        if ($mergePathsFound === false) {
            $normalizers[] = new Denormalizer\MergePaths();
        }

        return $normalizers;
    }
}
