<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Bucket\Executable;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * Prepend
 */
final class Prepend extends Common implements Executable
{
    /**
     * @param Bucket $bucket
     *
     * @throws Exception
     *
     * @return MetaDoc
     */
    public function execute(Bucket $bucket): MetaDoc
    {
        return $this->executeCommand($bucket, 'prepend');
    }
}
