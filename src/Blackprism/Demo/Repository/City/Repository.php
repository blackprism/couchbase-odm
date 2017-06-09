<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Bucket\Provider;
use Blackprism\CouchbaseODM\Bucket\ProviderAware;
use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Readable\Get;
use Blackprism\CouchbaseODM\Bucket\Readable\Query;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Repository\MappingFactoryAware;
use Blackprism\CouchbaseODM\Serializer\Decoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Decoder\MergePaths;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\Demo\Repository\City;
use Blackprism\Demo\Repository\Country;
use Blackprism\Demo\Repository\Mayor;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Serializer;

class Repository implements ProviderAware, MappingFactoryAware
{
    const BUCKET_NAME = 'odm-test';

    /**
     * @var Provider
     */
    private $bucketProvider;

    /**
     * @var MappingFactory
     */
    private $mappingFactory;

    public function providerIs(Provider $provider)
    {
        $this->bucketProvider = $provider;
    }

    public function mappingFactoryIs(MappingFactory $mappingFactory)
    {
        $this->mappingFactory = $mappingFactory;
    }

    public function getReadableBucket(): Readable\Bucket
    {
        return $this->bucketProvider->getReadableBucket(new BucketName(self::BUCKET_NAME));
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
        $metaDoc = (new Get($id))->execute($this->getReadableBucket());

        $normalizers = [
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

        $result = (new Query($n1ql))->execute($this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\Mapping(
                $this->mappingFactory->get(MappingDefinition::class)
            )
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[city]', 'array');
    }

    public function getCitiesWithMayorAndMapping()
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

        $result = (new Query($n1ql))->execute($this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\Mapping($this->mappingFactory->get(MappingDefinition::class))
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[]', 'array');
    }

    public function getCitiesAndMayorAndMapping()
    {
        $n1ql = '
            SELECT *
            FROM `odm-test` AS city
            LEFT JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE city.type = "city" AND mayor.type = "mayor"
            ORDER BY city.name';

        $result = (new Query($n1ql))->execute($this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\Mapping(
                $this->mappingFactory->get(MappingDefinition::class),
                $this->mappingFactory->get(Mayor\MappingDefinition::class)
            )
        ];

        $encoders = [
            new ArrayDecoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[mayor]', 'array');
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

        $result = (new Query($n1ql))->execute($this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\Collection(),
            new Denormalizer\Mapping(
                $this->mappingFactory->get(MappingDefinition::class)
            )
        ];

        $encoders = [
            new MergePaths('')
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[city]', MergePaths::class);
    }
}
