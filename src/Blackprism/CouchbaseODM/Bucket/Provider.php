<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket;

use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Writable;
use Blackprism\CouchbaseODM\Value\BucketName;
use Blackprism\CouchbaseODM\Value\BucketSetting;

interface Provider
{
    public function bucketSettings(BucketSetting ...$bucketSettings): Provider;
    public function getReadableBucket(BucketName $bucketName): Readable\Bucket;
    public function getWritableBucket(BucketName $bucketName): Writable\Bucket;
}
