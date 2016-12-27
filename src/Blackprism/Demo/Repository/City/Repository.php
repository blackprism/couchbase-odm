<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Serializer\Decoder\MergePaths;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class Repository implements ConnectionAwareInterface
{
    use ConnectionAwareTrait;

    const BUCKET_NAME = 'odm-test';

    /** @var Bucket */
    private $bucket;

    /**
     * @param string $type
     * @param array $normalizers
     * @param array $encoders
     *
     * @return SerializerInterface
     */
    public function getSerializer(string $type = '', array $normalizers = [], array $encoders = [])
    {
        $city = new City\Configuration\Denormalizer();
        $city->addDenormalize('country', new Country\Configuration\Denormalizer());
        $city->addDenormalize('mayor', new Country\Configuration\Denormalizer());

        $defaultNormalizers = [
            $city,
            new City\Configuration\Normalizer(),
            new Country\Configuration\Normalizer(),
            new Mayor\Configuration\Normalizer()
        ];

        $dispatchToType = new DispatchToType();
        $mergePaths = new MergePaths("inutile");
        $mergePaths->nextIs($dispatchToType);

        $encoders = [
            $mergePaths
        ];

        $normalizers = array_replace($defaultNormalizers, $normalizers);
        $serializer = new Serializer($normalizers, $encoders);

        if ($type !== '') {
            $serializer->typeIs($type);
        }

        return $serializer;
    }

    /**
     * @return Bucket
     */
    public function getBucket()
    {
        if ($this->bucket === null) {
            $this->bucket = $this->connection->getBucket(new BucketName(self::BUCKET_NAME));
        }

        return $this->bucket;
    }

    /**
     * @param mixed $documentId
     *
     * @return object
     * @throws \Blackprism\CouchbaseODM\Exception\Exception
     */
    public function get($documentId)
    {
        $metaDoc = $this->getBucket()->get($documentId);

        $normalizers = [
            new Denormalizer\CollectionExtractObjectWithKey(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
        ];

        $encoders = [
            new JsonDecode(true),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($metaDoc->value(), 'city', 'json');
    }

    /**
     * @param object $object
     */
    public function save($object)
    {
        $documents = $this->getSerializer()->serialize($object, 'array');

        foreach ($documents as $id => $document) {
            $this->getBucket()->update($id, $document);
        }
    }

    public function getJuice()
    {
        $n1ql = 'SELECT @juice FROM `odm-test` AS juice WHERE meta(@juice).id = "odwalla-juice1"';
        $result = $this->getBucket()->query($n1ql);

        return $this->getSerializer()->deserialize($result->rows(), Denormalizer\FirstObject::class, 'array');
    }

    public function getJuiceAsCity()
    {
        $n1ql = '
            SELECT
              @city,
              meta(@city).id AS `city.id`,
              @mayor AS `city.mayor`,
              meta(@mayor).id AS `city.mayor.id`
            FROM `odm-test` AS city
            WHERE meta(@city).id = "odwalla-juice1"';

        $result = $this->getBucket()->query($n1ql);

        $dispatchToType = new Denormalizer\DispatchToType();
        $dispatchToType->denormalizeTypelessWith(Denormalizer\Raw::class);
        $normalizers[Denormalizer\DispatchToType::class] = $dispatchToType;

        return $this->getSerializer('', $normalizers)->deserialize($result->rows(), Denormalizer\FirstObject::class, 'array');
    }

    /**
     * @return \Blackprism\Demo\Model\City
     */
    public function getCity1ByN1QL()
    {
        $n1ql = '
            SELECT @city
            FROM `odm-test` AS city
            WHERE meta(@city).id = "city-1"';

        $result = $this->getBucket()->query($n1ql);

        $normalizers = [
            new Denormalizer\DispatchToType(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), Denormalizer\DispatchToType::class . '[0]', 'array');
    }

    public function getCityWithMayor($id)
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
            JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE mayor.`internal-type` = "mayor"
              AND meta(@city).id = "' . $id . '"';

        $result = $this->getBucket()->query($n1ql);

        $normalizers = [
            new Denormalizer\DispatchToType('internal-type'),
            new City\Configuration\Denormalizer(),
            new City\Configuration\Denormalizer(),
            new Country\Configuration\Denormalizer(),
            new Mayor\Configuration\Denormalizer()
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), Denormalizer\DispatchToType::class . '[0]', 'array');
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

        $result = $this->getBucket()->query($n1ql);

        $normalizers = [
            new Denormalizer\DispatchToType(),
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

        return $serializer->deserialize($result->rows(), 'collection[][city]', 'array');
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

        $result = $this->getBucket()->query($n1ql);

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
