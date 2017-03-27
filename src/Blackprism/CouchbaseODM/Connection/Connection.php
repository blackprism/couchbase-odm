<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Writable;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\Dsn;
use Couchbase;

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
     * @var \CouchbaseCluster|null
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

    /**
     * @param BucketName $bucketName
     * @param string     $password
     *
     * @return \CouchbaseCluster
     */
    private function getConnectionForBucket(BucketName $bucketName, string $password = '')
    {
        if ($this->connection === null) {
            echo "La connexion est réellement faite !\n";
            $this->connection = new Couchbase\Cluster($this->dsn->value());
        }

        if ($password !== '') {
            $authenticator = new Couchbase\ClassicAuthenticator();
            $authenticator->bucket($bucketName->value(), $password);
            $this->connection->authenticate($authenticator);
        }

        return $this->connection;
    }

    /**
     * @param BucketName $bucketName
     * @param string     $password
     *
     * @return Readable\Bucket
     */
    public function getReadableBucket(BucketName $bucketName, string $password = ''): Readable\Bucket
    {
        $couchbaseBucket = $this->getConnectionForBucket($bucketName, $password)->openBucket($bucketName->value());

        return new Readable\Bucket($couchbaseBucket);
    }

    /**
     * @param BucketName $bucketName
     * @param string     $password
     *
     * @return Writable\Bucket
     */
    public function getWritableBucket(BucketName $bucketName, string $password = ''): Writable\Bucket
    {
        $couchbaseBucket = $this->getConnectionForBucket($bucketName, $password)->openBucket($bucketName->value());

        return new Writable\Bucket($couchbaseBucket);
    }
}
