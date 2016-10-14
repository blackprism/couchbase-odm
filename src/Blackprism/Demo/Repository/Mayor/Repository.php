<?php

namespace Blackprism\Demo\Repository\Mayor;

use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Symfony\Component\Serializer\SerializerInterface;

class Repository implements ConnectionAwareInterface
{
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
    private function getSerializer(array $normalizers = [], array $encoders = [])
    {
        if ($normalizers === []) {
            $normalizers = [
                new Denormalizer\MergePaths(Denormalizer\FirstObject::class),
                new Denormalizer\FirstObject(),
                new Mayor\Configuration\Denormalizer()
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

    public function getMayors()
    {
        $n1ql = '
            SELECT
              meta(@mayor).id AS `mayor.id`,
              @mayor
            FROM `odm-test` AS mayor
            WHERE mayor.type = "mayor"
            ORDER BY mayor.firstname';

        $result = $this->getBucket()->query($n1ql);

        return $this->getSerializer()->deserialize($result->rows(), Denormalizer\Collection::class, 'array');
    }
}
