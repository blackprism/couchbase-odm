<?php

namespace Blackprism\Demo\Repository\Mayor;

use Blackprism\CouchbaseODM\Bucket\Provider;
use Blackprism\CouchbaseODM\Bucket\ProviderAware;
use Blackprism\CouchbaseODM\Repository\MappingFactory;
use Blackprism\CouchbaseODM\Repository\MappingFactoryAware;
use Blackprism\CouchbaseODM\Serializer\Decoder\ArrayDecoder;
use Blackprism\CouchbaseODM\Serializer\Denormalizer;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Bucket\Readable;
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

    public function getMayors()
    {
        $n1ql = '
            SELECT
              meta(@mayor).id AS `mayor.id`,
              @mayor
            FROM `odm-test` AS mayor
            WHERE mayor.type = "mayor"
            ORDER BY mayor.firstname';

        $result = (new Readable\Query($n1ql))->execute($this->getReadableBucket());


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

        return $serializer->deserialize($result->rows(), 'collection[mayor]', 'array');
    }
}
