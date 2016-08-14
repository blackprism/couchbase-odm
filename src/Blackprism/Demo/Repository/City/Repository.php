<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryAwareInterface;
use Blackprism\CouchbaseODM\Serializer\SerializerFactoryAwareTrait;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\DocumentId;
use Symfony\Component\Serializer\SerializerInterface;

class Repository implements SerializerFactoryAwareInterface, ConnectionAwareInterface
{
    use SerializerFactoryAwareTrait;
    use ConnectionAwareTrait;

    const BUCKET_NAME = 'odm-test';

    /** @var Bucket */
    private $bucket;

    /**
     * @param array $normalizers
     * @param array $encoders
     *
     * @return SerializerInterface
     */
    public function getSerializer(array $normalizers = [], array $encoders = [])
    {
        if ($normalizers === []) {
            $normalizers = [
                new City\Configuration\Denormalizer(),
                new City\Configuration\Normalizer(),
                new Country\Configuration\Denormalizer(),
                new Country\Configuration\Normalizer(),
                new Mayor\Configuration\Denormalizer(),
                new Mayor\Configuration\Normalizer()
            ];
        }

        return $this->serializerFactory->get($normalizers, $encoders);
    }

    private function getBucket()
    {
        if ($this->bucket === null) {
            $this->bucket = $this->connection->getBucket(new BucketName(self::BUCKET_NAME));
        }

        return $this->bucket;
    }

    /**
     * @param DocumentId $documentId
     * @param string $type
     *
     * @return mixed
     */
    public function get(DocumentId $documentId, string $type = Denormalizer\MergePaths::DENORMALIZATION_TYPE_OUTPUT)
    {
        $metaDoc = $this->getBucket()->get($documentId);

        return $this->getSerializer()->deserialize($metaDoc->value(), $type, 'json');
    }

    public function save(array $documents)
    {
        foreach ($documents as $id => $document) {
            $this->getBucket()->update(new DocumentId($id), $document);
        }
    }

    public function getCitiesWithMayor()
    {
        $n1ql = '
            SELECT 
              @city,
              meta(@city).id AS `city.id`,
              @mayor AS `city.mayor`,
              meta(@mayor).id AS `city.mayor.id`
            FROM `odm-test` AS city
            JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE city.type = "city" AND mayor.type = "mayor"
            ORDER BY city.name';

        $result = $this->getBucket()->query($n1ql);

        return $this->getSerializer()->deserialize($result->rows(), Denormalizer\Collection::class, 'array');
    }
}
