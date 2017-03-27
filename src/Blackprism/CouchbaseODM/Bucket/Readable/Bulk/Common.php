<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable\Bulk;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * Common
 */
abstract class Common
{
    /**
     * @var array
     */
    protected $identifiers;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param array $identifiers
     * @param array $options
     */
    public function __construct(array $identifiers, array $options = [])
    {
        $this->identifiers = $identifiers;
        $this->options     = $options;
    }

    /**
     * @param Bucket $bucket
     * @param string $command
     *
     * @throws Exception
     *
     * @return MetaDoc[]
     */
    protected function executeCommand(Bucket $bucket, string $command): array
    {
        return $bucket->bulkReadCommand($command, $this->identifiers, $this->options);
    }
}
