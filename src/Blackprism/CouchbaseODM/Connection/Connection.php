<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\Dsn;

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
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var \CouchbaseCluster
     */
    private $connection;

    /**
     * @param Dsn $dsn
     * @param string $username
     * @param string $password
     */
    public function __construct(Dsn $dsn, string $username = '', string $password = '')
    {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
    }

    private function getConnection()
    {
        if ($this->connection === null) {
            echo "La connexion est réellement faite !\n";
            $this->connection = new \CouchbaseCluster($this->dsn->value(), $this->username, $this->password);
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

        return new Bucket($couchbaseBucket);
    }
}
