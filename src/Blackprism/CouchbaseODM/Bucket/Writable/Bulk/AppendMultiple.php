<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable\Bulk;

use Blackprism\CouchbaseODM\Bucket\Writable\Bucket;
use Blackprism\CouchbaseODM\Bucket\Writable\Writable;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * AppendMultiple
 */
final class AppendMultiple extends Common implements Writable
{
    /**
     * @param Bucket $bucket
     *
     * @throws Exception
     *
     * @return MetaDoc[]
     */
    public function execute(Bucket $bucket): array
    {
        return $this->executeCommand($bucket, 'append');
    }
}
