<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareInterface;
use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Encoder\ArrayEncoder;
use Blackprism\CouchbaseODM\Serializer\InputOutput;
use Blackprism\CouchbaseODM\Serializer\Serializer;
use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareInterface;
use Blackprism\CouchbaseODM\Connection\ConnectionAwareTrait;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\DocumentId;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class Repository implements ConnectionAwareInterface, PropertyChangedListenerAwareInterface
{
    use ConnectionAwareTrait;
    use PropertyChangedListenerAwareTrait;

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
        $defaultNormalizers = [
            new City\Configuration\Denormalizer(),
            new City\Configuration\Normalizer(),
            new Country\Configuration\Denormalizer(),
            new Country\Configuration\Normalizer(),
            new Mayor\Configuration\Denormalizer(),
            new Mayor\Configuration\Normalizer()
        ];

        $normalizers = array_replace($defaultNormalizers, $normalizers);
        $serializer = new Serializer($normalizers, $encoders);

        if ($type !== '') {
            $serializer->typeIs($type);
        }

        $serializer->propertyChangedListenerIs($this->propertyChangedListener);

        return $serializer;
    }

    public function getBucket()
    {
        if ($this->bucket === null) {
            $this->bucket = $this->connection->getBucket(new BucketName(self::BUCKET_NAME));
        }

        return $this->bucket;
    }

    /**
     * @param        $documentId
     * @param string $type
     *
     * @return object
     * @throws \Blackprism\CouchbaseODM\Exception\Exception
     */
    public function get($documentId)
    {
        $metaDoc = $this->getBucket()->get($documentId);
        return $this->getSerializer('city')->deserialize($metaDoc->value(), Denormalizer\MergePaths::class, 'json');
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

    public function getCityWithMayor($id)
    {
        $n1ql = '
            SELECT
              @city,
              meta(@city).id AS `city.id`,
              @mayor AS `city.mayor`,
              meta(@mayor).id AS `city.mayor.id`
            FROM `odm-test` AS city
            JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE mayor.`internal-type` = "mayor"
              AND meta(@city).id = "' . $id . '"';

        $result = $this->getBucket()->query($n1ql);

        $dispatchToType = new Denormalizer\DispatchToType();
        $dispatchToType->typePropertyIs('internal-type');
        $normalizers[Denormalizer\DispatchToType::class] = $dispatchToType;

        return $this->getSerializer('', $normalizers)->deserialize($result->rows(), Denormalizer\FirstObject::class, 'array');
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

        $normalizers[Denormalizer\MergePaths::class]  = new Denormalizer\MergePaths(Denormalizer\FirstObject::class);
        $normalizers[Denormalizer\FirstObject::class] = new Denormalizer\FirstObject(Denormalizer\DispatchToType::class);

        return $this->getSerializer('')->deserialize($result->rows(), Denormalizer\CollectionFirstObject::class, 'array');
    }
}
