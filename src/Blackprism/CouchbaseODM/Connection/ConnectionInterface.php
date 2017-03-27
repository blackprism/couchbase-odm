<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Writable;
use Blackprism\CouchbaseODM\Value\BucketName;

/**
 * Interface Connection
 */
interface ConnectionInterface
{
    /**
     * @param BucketName $bucketName
     *
     * @return Readable\Bucket
     */
    public function getReadableBucket(BucketName $bucketName): Readable\Bucket;

    /**
     * @param BucketName $bucketName
     *
     * @return Writable\Bucket
     */
    public function getWritableBucket(BucketName $bucketName): Writable\Bucket;
}
