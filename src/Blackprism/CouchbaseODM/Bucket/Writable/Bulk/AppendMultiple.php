<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable\Bulk;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Bucket\Executable;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * AppendMultiple
 */
final class AppendMultiple extends Common implements Executable
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
