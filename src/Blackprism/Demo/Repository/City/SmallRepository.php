<?php

namespace Blackprism\Demo\Repository\City;

use Blackprism\CouchbaseODM\Bucket\CanInvoke;
use Blackprism\CouchbaseODM\Bucket\Provider;
use Blackprism\CouchbaseODM\Bucket\ProviderAware;
use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Readable\Query;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Repository\MappingFactoryAware;
use Blackprism\CouchbaseODM\Serializer\Decoder\ArrayDecoder;
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

        $serializer = new Serializer($normalizers, [new ArrayDecoder()]);

        return $serializer->deserialize($result->rows(), 'collection[mayor]', 'array');
    }
}
