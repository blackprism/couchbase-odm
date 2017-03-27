<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket;

use Blackprism\CouchbaseODM\Bucket\Readable;
use Blackprism\CouchbaseODM\Bucket\Writable;
use Blackprism\CouchbaseODM\Exception\Exception;

/**
 * Interface Invokable
 */
interface Invokable
{
    /**
     * @param Readable\Readable $readable
     * @param Readable\Bucket   $bucket
     *
     * @throws Exception
     * @return mixed
     */
    public function invokeReader(Readable\Readable $readable, Readable\Bucket $bucket);

    /**
     * @param Writable\Writable $writable
     * @param Writable\Bucket   $bucket
     *
     * @return mixed
     */
    public function invokeWriter(Writable\Writable $writable, Writable\Bucket $bucket);
}
