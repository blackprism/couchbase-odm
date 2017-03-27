<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable\Bulk;

use Blackprism\CouchbaseODM\Bucket\Bucket;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;
use Blackprism\CouchbaseODM\Value\StringCollection;

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
     * @var StringCollection
     */
    protected $identifiers;

    /**
     * @var mixed|null
     */
    protected $values;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Bucket           $bucket
     * @param StringCollection $identifiers
     * @param mixed            $values
     * @param array            $options
     */
    public function __construct(Bucket $bucket, StringCollection $identifiers, $values = null, array $options = [])
    {
        $this->bucket      = $bucket;
        $this->identifiers = $identifiers;
        $this->values      = $values;
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
        return $bucket->bulkWriteCommand($command, $this->identifiers, $this->values, $this->options);
    }
}
