<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable;

use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * GetFromReplica
 */
final class GetFromReplica extends Common implements Readable
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
        return $this->executeCommand($bucket, 'getFromReplica');
    }
}
