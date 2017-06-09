<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable\Bulk;

use Blackprism\CouchbaseODM\Bucket\Readable\Bucket;
use Blackprism\CouchbaseODM\Bucket\Readable\Readable;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * GetMulti
 */
final class GetMulti extends Common implements Readable
{
    /**
     * @param Bucket $bucket
     *
     * @throws Exception
     *
     * @return array|MetaDoc[]
     */
    public function execute(Bucket $bucket): array
    {
        return $this->executeCommand($bucket, 'getMulti');
    }
}
