<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable;

use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\Result;

/**
 * Query
 */
final class Query implements Readable
{
    /**
     * @var string
     */
    protected $query;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $query
     * @param array  $options
     */
    public function __construct(string $query, array $options = [])
    {
        $this->query   = $query;
        $this->options = $options;
    }

    /**
     * @param Bucket $bucket
     *
     * @throws Exception
     *
     * @return Result
     */
    public function execute(Bucket $bucket): Result
    {
        return $bucket->queryCommand($this->query, $this->options);
    }
}
