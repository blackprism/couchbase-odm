<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * Common
 */
abstract class Common
{
    /**
     * @var Bucket
     */
    protected $bucket;

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var mixed|null
     */
    protected $values;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Bucket $bucket
     * @param string $identifier
     * @param mixed  $values
     * @param array  $options
     */
    public function __construct(Bucket $bucket, string $identifier, $values = null, array $options = [])
    {
        $this->bucket     = $bucket;
        $this->identifier = $identifier;
        $this->values     = $values;
        $this->options    = $options;
    }

    /**
     * @param Bucket $bucket
     * @param string $command
     *
     * @throws Exception
     *
     * @return MetaDoc
     */
    protected function executeCommand(Bucket $bucket, string $command): MetaDoc
    {
        return $bucket->writeCommand($command, $this->identifier, $this->values, $this->options);
    }
}
