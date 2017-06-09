<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Bucket\Readable;

use Blackprism\CouchbaseODM\Bucket\transcodersAware;
use Blackprism\CouchbaseODM\Exception\Bucket\OperationNotFound;
use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;
use Blackprism\CouchbaseODM\Exception\ExceptionNormalizer;
use Blackprism\CouchbaseODM\Value\Couchbase\Result;
use Couchbase;

/**
 * Bucket
 */
final class Bucket implements transcodersAware
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
     * @param string $query
     * @param array  $options
     *
     * @throws Exception
     *
     * @return Result
     */
    public function queryCommand(string $query, array $options = []): Result
    {
        try {
            return new Result($this->bucket->query($this->getN1qlQueryFromString($query, $options), true));
        } catch (Couchbase\Exception $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }
    }

    /**
     * @param string $query
     * @param array  $options
     *
     * @return Couchbase\N1qlQuery
     */
    private function getN1qlQueryFromString(string $query, array $options = []): Couchbase\N1qlQuery
    {
        if ($options === []) {
            return Couchbase\N1qlQuery::fromString($query);
        }

        $n1qlQuery = Couchbase\N1qlQuery::fromString($query);
        // @TODO https://forums.couchbase.com/t/n1qlquery-missing-options-in-stub/12132
        $n1qlQuery->options = $options;

        return $n1qlQuery;
    }

    /**
     * @param string $command
     * @param string $identifier
     * @param array  $options
     *
     * @throws Exception
     *
     * @return MetaDoc
     */
    public function readCommand(string $command, string $identifier, array $options = []): MetaDoc
    {
        try {
            return new MetaDoc($this->normalizeReadCommand($command, $identifier, $options));
        } catch (Couchbase\Exception $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }
    }

    /**
     * @param string $command
     * @param array  $identifiers
     * @param array  $options
     *
     * @throws Exception
     *
     * @return MetaDoc[]
     */
    public function bulkReadCommand(string $command, array $identifiers, array $options = []): array
    {
        $couchbaseMetaDocs = $this->normalizeReadCommand($command, $identifiers, $options);
        $metaDocs = [];

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $metaDocs[] = new MetaDoc($couchbaseMetaDoc);
        }

        return $metaDocs;
    }

    /**
     * @param string       $operation
     * @param string|array $identifier
     * @param array        $options
     *
     * @throws Exception
     *
     * @return Couchbase\Document
     */
    private function normalizeReadCommand(string $operation, $identifier, array $options = []): Couchbase\Document
    {
        switch ($operation) {
            case 'get':
                return $this->bucket->get($identifier, $options);
            case 'getFromReplica':
                return $this->bucket->getFromReplica($identifier, $options);
        }

        throw new OperationNotFound($operation);
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
