<?php

namespace Blackprism\Demo\Repository\Mayor;

use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Connection;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class Repository
{
    const BUCKET = 'odm-test';

    /** @var  Connection */
    private $connection;

    /** @var  Bucket */
    private $bucket;

    /** @var Serializer */
    private $serializer;

    public function __construct()
    {
        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\MergePaths(Denormalizer\FirstObject::class),
            new Denormalizer\FirstObject(),
            new Mayor\Configuration\Denormalizer()
        ];

        $encoders = array(new JsonEncoder());

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function connectionIs(Connection $connection)
    {
        $this->connection = $connection;
    }

    private function getBucket()
    {
        if ($this->bucket === null) {
            $this->bucket = $this->connection->getBucket(new BucketName(self::BUCKET), $this->serializer);
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

        return $this->getBucket()->deserialize($result);
    }
}
