<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket;
use Blackprism\CouchbaseODM\Value\BucketName;

/**
 * Interface Connection
 */
interface ConnectionInterface
{
    /**
     * @param BucketName $bucketName
     *
     * @return Bucket
     */
    public function getBucket(BucketName $bucketName): Bucket;
}
