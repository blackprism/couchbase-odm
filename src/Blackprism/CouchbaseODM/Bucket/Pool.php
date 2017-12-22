<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket;

use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\BucketSetting;
use Couchbase;
use RuntimeException;

/**
 * Pool
 */
class Pool implements Provider
{
    /**
     * @var BucketSetting[]
     */
    private $bucketSettings = [];

    /**
     * @var Readable\Bucket[]
     */
    private $readableBuckets = [];

    /**
     * @var Writable\Bucket[]
     */
    private $writableBuckets = [];


    /**
     * @param BucketSetting[int] ...$bucketSettings
     *
     * @return Provider
     */
    public function bucketSettings(BucketSetting ...$bucketSettings): Provider
    {
        foreach ($bucketSettings as $bucketSetting) {
            $this->bucketSettings[$bucketSetting->bucket()] = $bucketSetting;
        }

        return $this;
    }

    /**
     * @param BucketName $bucketName
     *
     * @throws RuntimeException
     *
     * @return Readable\Bucket
     */
    public function getReadableBucket(BucketName $bucketName): Readable\Bucket
    {
        $bucketSetting = $this->getBucketSetting($bucketName);
        $identifier = $this->getPoolIdentifier($bucketSetting->dsn(), $bucketSetting->bucket());

        if (isset($this->readableBuckets[$identifier]) === true) {
            return $this->readableBuckets[$identifier];
        }

        $cluster = $this->getCluster($bucketSetting);
        $this->readableBuckets[$identifier] = new Readable\Bucket($cluster->openBucket($bucketSetting->bucket()));
        $this->transcodersAre($this->readableBuckets[$identifier]);

        return $this->readableBuckets[$identifier];
    }

    /**
     * @param BucketName $bucketName
     *
     * @throws RuntimeException
     *
     * @return Writable\Bucket
     */
    public function getWritableBucket(BucketName $bucketName): Writable\Bucket
    {
        $bucketSetting = $this->getBucketSetting($bucketName);
        $identifier = $this->getPoolIdentifier($bucketSetting->dsn(), $bucketSetting->bucket());

        if (isset($this->writableBuckets[$identifier]) === true) {
            return $this->writableBuckets[$identifier];
        }

        $cluster = $this->getCluster($bucketSetting);
        $this->writableBuckets[$identifier] = new Writable\Bucket($cluster->openBucket($bucketName->value()));
        $this->transcodersAre($this->writableBuckets[$identifier]);

        return $this->writableBuckets[$identifier];
    }

    private function getBucketSetting(BucketName $bucketName): BucketSetting
    {
        if (isset($this->bucketSettings[$bucketName->value()]) === false) {
            // @todo rewrite and add phpdoc
            throw new RuntimeException("Config not found");
        }

        return $this->bucketSettings[$bucketName->value()];
    }

    private function getCluster(BucketSetting $bucketSetting): Couchbase\Cluster
    {
        echo "La connexion est réellement faite !\n";
        $cluster = new Couchbase\Cluster($bucketSetting->dsn());

        if ($bucketSetting->password() !== '') {
            echo "On auth le bucket {$bucketSetting->bucket()} avec {$bucketSetting->password()} !\n";
            $authenticator = new Couchbase\ClassicAuthenticator();
            $authenticator->bucket($bucketSetting->bucket(), $bucketSetting->password());
            $cluster->authenticate($authenticator);
        }

        return $cluster;
    }

    private function getPoolIdentifier(string $dsn, string $bucketName): string
    {
        return $dsn . '/' . $bucketName;
    }

    private function transcodersAre(transcodersAware $bucket)
    {
        $bucket->transcodersAre(
            /** @return mixed */
            function ($data) {
                return $data;
            },
            /** @return mixed */
            function ($bytes) {
                return $bytes;
            }
        );
    }
}
