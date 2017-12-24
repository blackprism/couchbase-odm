<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Bucket\Provider;
use Blackprism\CouchbaseODM\Bucket\ProviderAware;
use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Readable\Query;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Repository\MappingFactoryAware;
use Blackprism\CouchbaseODM\Serializer\Decoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Decoder\MergePaths;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\Demo\Repository\Mayor;
use Symfony\Component\Serializer\Serializer;

class SmallRepository implements ProviderAware, MappingFactoryAware
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
     * @throws \Blackprism\CouchbaseODM\Exception\Exception
     * @throws \Symfony\Component\Serializer\Exception\NotEncodableValueException
     * @throws \Symfony\Component\Serializer\Exception\NotNormalizableValueException
     *
     * @return object
     */
    public function getCitiesWithMayor()
    {
        $n1ql = '
            SELECT
                city,
                meta(city).id as `city.id`,
                mayor as `city.mayor`,
                meta(mayor).id as `city.mayor.id`
            FROM `odm-test` AS city
            LEFT JOIN `odm-test` AS mayor ON KEYS city.mayorId
            WHERE city.type = "city" AND mayor.type = "mayor"
            ORDER BY city.name';

        $result = (new Readable\Query($n1ql))->execute($this->getReadableBucket());

        $normalizers = [
            new Denormalizer\Collection(),
            new Denormalizer\Mapping(
                $this->mappingFactory->get(new MappingDefinition())
            )
        ];

        $encoders = [
            new MergePaths()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->deserialize($result->rows(), 'collection[city]', MergePaths::class);
    }


    public function getCitiesAndMayor()
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
                $this->mappingFactory->get(new MappingDefinition()),
                $this->mappingFactory->get(new Mayor\MappingDefinition())
            )
        ];

        $serializer = new Serializer($normalizers, [new ArrayDecoder()]);

        return $serializer->deserialize($result->rows(), 'collection[]', 'array');
    }
}
