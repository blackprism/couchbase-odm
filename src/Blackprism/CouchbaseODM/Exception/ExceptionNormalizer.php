<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Exception;

use Blackprism\CouchbaseODM\Exception\Bucket\KeyAlreadyExist;
use Blackprism\CouchbaseODM\Exception\Bucket\NoSuchKey;
use Blackprism\CouchbaseODM\Exception\Bucket\Unknown;

class ExceptionNormalizer
{
    public static function normalize(\CouchbaseException $exception): Exception
    {
        switch ($exception->getCode()) {
            case COUCHBASE_KEY_ENOENT:
                return new NoSuchKey($exception->getMessage(), $exception->getCode());

            case COUCHBASE_KEY_EEXISTS:
                return new KeyAlreadyExist($exception->getMessage(), $exception->getCode());

            default:
                return new Unknown($exception->getMessage(), $exception->getCode());
        }
    }
}
