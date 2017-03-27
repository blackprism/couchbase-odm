<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable;

use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;

/**
 * Common
 */
abstract class Common
{
    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param string $identifier
     * @param array  $options
     */
    public function __construct(string $identifier, array $options = [])
    {
        $this->identifier = $identifier;
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
        return $bucket->readCommand($command, $this->identifier, $this->options);
    }
}
