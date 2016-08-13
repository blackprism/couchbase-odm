<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\Dsn;
use Blackprism\CouchbaseODM\Value\N1qlHosts;

/**
 * Connection
 */
final class Connection implements ConnectionInterface
{
    /**
     * @var Dsn
     */
    private $dsn;

    /**
     * @var \CouchbaseCluster
     */
    private $connection;

    /**
     * @var N1qlHosts
     */
    private $n1qlHosts;

    /**
     * @param Dsn $dsn
     */
    public function __construct(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    public function n1qlHosts(N1qlHosts $n1qlHosts)
    {
        $this->n1qlHosts = $n1qlHosts;
    }

    private function getConnection()
    {
        if ($this->connection === null) {
            echo "La connexion est réellement faite !\n";
            $this->connection = new \CouchbaseCluster($this->dsn->value());
        }

        return $this->connection;
    }

    /**
     * @param BucketName $bucketName
     *
     * @return Bucket
     */
    public function getBucket(BucketName $bucketName): Bucket
    {
        $couchbaseBucket = $this->getConnection()->openBucket($bucketName->value());

        if ($this->n1qlHosts !== false) {
            $couchbaseBucket->enableN1ql($this->n1qlHosts);
        }

        return new Bucket($couchbaseBucket);
    }
}
