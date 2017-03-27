<?php

namespace Blackprism\CouchbaseODM\Connection;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Bucket\Writable\Bulk\AppendMultiple;
use Blackprism\CouchbaseODM\Bucket\Writable\Replace;
use Blackprism\CouchbaseODM\Serializer\Denormalizer\DenormalizeIterator;
use Psr\Log\LoggerInterface;

/**
 * Trait ConnectionAwareTrait
 */
trait ConnectionAwareTrait
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function connectionIs(
        ConnectionInterface $connection,
        LoggerInterface $logger,
        DenormalizeIterator $denormalizeIterator,
        Bucket $bucket,
        AppendMultiple $appendMultiple,
        Replace $replace
    ) {
        $this->connection = $connection;
    }
}
