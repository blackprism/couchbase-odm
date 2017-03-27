<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Writable;

use Blackprism\CouchbaseODM\Exception\Bucket\OperationNotFound;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;
use Blackprism\CouchbaseODM\Exception\ExceptionNormalizer;
use Blackprism\CouchbaseODM\Value\StringCollection;
use Couchbase;

/**
 * Bucket
 */
final class Bucket
{
    /**
     * @var Couchbase\Bucket
     */
    private $bucket;

    /**
     * @param Couchbase\Bucket $bucket
     */
    public function __construct(Couchbase\Bucket $bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @param string $command
     * @param string $identifier
     * @param mixed  $values
     * @param array  $options
     *
     * @throws Exception
     *
     * @return MetaDoc
     */
    public function writeCommand(string $command, string $identifier, $values = null, array $options = []): MetaDoc
    {
        try {
            return new MetaDoc($this->normalizeWriteCommand($command, $identifier, $values, $options));
        } catch (Couchbase\Exception $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }
    }

    /**
     * @param string           $command
     * @param StringCollection $identifiers
     * @param mixed            $values
     * @param array            $options
     *
     * @throws Exception
     * @return MetaDoc[]
     */
    public function bulkWriteCommand(
        string $command,
        StringCollection $identifiers,
        $values = null,
        array $options = []
    ): array {
        $couchbaseMetaDocs = $this->normalizeWriteCommand($command, $identifiers, $values, $options);
        $metaDocs = [];

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $metaDocs[] = new MetaDoc($couchbaseMetaDoc);
        }

        return $metaDocs;
    }

    /**
     * @param string                  $command
     * @param string|StringCollection $identifier
     * @param mixed                   $values
     * @param array                   $options
     *
     * @throws Exception
     * @return Couchbase\Document
     */
    private function normalizeWriteCommand(
        string $command,
        $identifier,
        $values = null,
        array $options = []
    ): Couchbase\Document {
        // @TODO et niveau perf on passe d'un array à un iterator à un array
        if ($identifier instanceof StringCollection) {
            $identifier = iterator_to_array($identifier);
        }

        switch ($command) {
            case 'insert':
                return $this->bucket->insert($identifier, $values, $options);
            case 'upsert':
                return $this->bucket->upsert($identifier, $values, $options);
            case 'replace':
                return $this->bucket->replace($identifier, $values, $options);
            case 'append':
                return $this->bucket->append($identifier, $values, $options);
            case 'prepend':
                return $this->bucket->prepend($identifier, $values, $options);
        }

        throw new OperationNotFound($command);
    }

    /**
     * @param callable $encoder
     * @param callable $decoder
     */
    public function transcodersAre(callable $encoder, callable $decoder)
    {
        $this->bucket->setTranscoder($encoder, $decoder);
    }
}
