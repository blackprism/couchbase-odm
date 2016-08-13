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

        $normalizers = $this->initNormalizers($normalizers);
        $normalizers = $this->addCollectionIfNotFound($normalizers);
        $normalizers = $this->addMergePathsIfNotFound($normalizers);

        $encoders[] = new JsonEncoder();
        $encoders[] = new ArrayDecoder();

        $this->serializers[$identifier] = new Serializer($normalizers, $encoders);

        return $this->serializers[$identifier];
    }

    /**
     * @param array $normalizers
     *
     * @return array
     */
    private function initNormalizers(array $normalizers)
    {
        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof PropertyChangedListenerAwareInterface) {
                $normalizer->propertyChangedListenerIs($this->propertyChangedListener);
            }
        }

        return $normalizers;
    }

    /**
     * @param array $normalizers
     *
     * @return array
     */
    private function addCollectionIfNotFound(array $normalizers)
    {
        $collectionFound = false;

        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof Denormalizer\Collection) {
                $collectionFound = true;
            }
        }

        if ($collectionFound === false) {
            $normalizers[] = new Denormalizer\Collection();
        }

        return $normalizers;
    }

    /**
     * @param array $normalizers
     *
     * @return array
     */
    private function addMergePathsIfNotFound(array $normalizers)
    {
        $mergePathsFound = false;

        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof Denormalizer\MergePaths) {
                $mergePathsFound = true;
            }
        }

        if ($mergePathsFound === false) {
            $normalizers[] = new Denormalizer\MergePaths();
        }

        return $normalizers;
    }
}
