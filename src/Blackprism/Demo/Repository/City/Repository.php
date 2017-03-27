<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Invokable;
use Blackprism\CouchbaseODM\Bucket\Readable\Get;
use Blackprism\CouchbaseODM\Bucket\Readable\Query;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionInterface;
use Blackprism\CouchbaseODM\Serializer\Decoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Decoder\MergePaths;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Serializer;

class Repository implements ConnectionAwareInterface
{
    const BUCKET_NAME = 'odm-test';

    /** @var Readable\Bucket */
    private $bucket;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    private $invoker;

    public function __construct(Invokable $invoker)
    {
        $this->invoker = $invoker;
    }

    /**
     * @param ConnectionInterface $connection
     */
    public function connectionIs(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Readable\Bucket
     */
    public function getReadableBucket()
    {
        if ($this->bucket === null) {
            $this->bucket = $this->connection->getReadableBucket(new BucketName(self::BUCKET_NAME));
        }

        return $this->bucket;
    }

    /**
     * @param string $id
     *
     * @throws \Blackprism\CouchbaseODM\Exception\Exception
     *
     * @return object
     */
    public function get(string $id)
    {
        $metaDoc = $this->invoker->invokeReader(new Get($id), $this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
        ];

        $encoders = [
            new JsonDecode(true),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($metaDoc->value(), 'city', 'json');
    }


    public function getCitiesWithMayor()
    {
        $n1ql = '
            SELECT
              OBJECT_CONCAT(
                @city,
                {
                  meta(@city).id,
                  "mayor": OBJECT_CONCAT(
                    @mayor,
                    {
                      meta(@mayor).id
                    }
                  )
                }
              ) as city
            FROM `odm-test` AS city
            LEFT JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE city.type = "city" AND mayor.type = "mayor"
            ORDER BY city.name';

        $result = $this->invoker->invokeReader(new Query($n1ql), $this->getReadableBucket());

        $normalizers = [
            new Denormalizer\DispatchToType(),
            new Denormalizer\Collection(),
            new Denormalizer\DispatchToType2(),
            new Denormalizer\Collection(),
            new City\Configuration\Denormalizer(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
            new Mayor\Configuration\Denormalizer()
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[1][city]', 'array');
    }


    public function getCitiesWithMayorAndMergePath()
    {
        $n1ql = '
            SELECT
                @city,
                meta(@city).id as `city.id`,
                @mayor as `city.mayor`,
                meta(@mayor).id as `city.mayor.id`
            FROM `odm-test` AS city
            LEFT JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE city.type = "city" AND mayor.type = "mayor"
            ORDER BY city.name';

        $result = $this->invoker->invokeReader(new Query($n1ql), $this->getReadableBucket());

        $normalizers = [
            new Denormalizer\DispatchToType(),
            new Denormalizer\Collection(),
            new City\Configuration\Denormalizer(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
            new Mayor\Configuration\Denormalizer()
        ];

        $encoders = [
            new MergePaths('')
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[][city]', MergePaths::class);
    }
}
