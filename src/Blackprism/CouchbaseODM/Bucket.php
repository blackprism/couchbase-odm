<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM;

use Blackprism\CouchbaseODM\Exception\Exception;
use Blackprism\CouchbaseODM\Exception\ExceptionNormalizer;
use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;
use Blackprism\CouchbaseODM\Value\Couchbase\Result;

/**
 * Bucket
 *
 * @todo options on Query https://forums.couchbase.com/t/php-sdk-running-query-against-2-buckets/10717/4
 */
final class Bucket
{
    /**
     * @var \CouchbaseBucket
     */
    private $bucket;

    /**
     * @param \CouchbaseBucket $bucket
     */
    public function __construct(\CouchbaseBucket $bucket)
    {
        $this->bucket = $bucket;
    }

    /**
     * @param string $operation
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    private function singleOperation(string $operation, string $id, $values = null, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->$operation($id, $values, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string  $operation
     * @param array[] $ids
     * @param mixed   $values
     * @param array   $options
     *
     * @return MetaDoc[]
     */
    private function multipleOperation(string $operation, array $ids, $values = null, array $options = []): array
    {
        $couchbaseMetaDocs = $this->bucket->$operation($ids, $values, $options);

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $couchbaseMetaDoc = new MetaDoc($couchbaseMetaDoc);
        }

        return $couchbaseMetaDocs;
    }

    /**
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function insert(string $id, $values = null, array $options = []): MetaDoc
    {
        return $this->singleOperation('insert', $id, $values, $options);
    }

    /**
     * @param array $ids
     * @param mixed $values
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function insertMulti(array $ids, $values = null, array $options = []): array
    {
        return $this->multipleOperation('insert', $ids, $values, $options);
    }

    /**
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function upsert(string $id, $values = null, array $options = []): MetaDoc
    {
        return $this->singleOperation('upsert', $id, $values, $options);
    }

    /**
     * @param array $ids
     * @param mixed $values
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function upsertMulti(array $ids, $values = null, array $options = []): array
    {
        return $this->multipleOperation('upsert', $ids, $values, $options);
    }

    /**
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function replace(string $id, $values = null, array $options = []): MetaDoc
    {
        return $this->singleOperation('replace', $id, $values, $options);
    }

    /**
     * @param array $ids
     * @param mixed $values
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function replaceMulti(array $ids, $values = null, array $options = []): array
    {
        return $this->multipleOperation('replace', $ids, $values, $options);
    }

    /**
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function append(string $id, $values = null, array $options = []): MetaDoc
    {
        return $this->singleOperation('append', $id, $values, $options);
    }

    /**
     * @param array $ids
     * @param mixed $values
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function appendMulti(array $ids, $values = null, array $options = []): array
    {
        return $this->multipleOperation('append', $ids, $values, $options);
    }

    /**
     * @param string $id
     * @param mixed  $values
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function prepend(string $id, $values = null, array $options = []): MetaDoc
    {
        return $this->singleOperation('prepend', $id, $values, $options);
    }

    /**
     * @param array $ids
     * @param mixed $values
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function prependMulti(array $ids, $values = null, array $options = []): array
    {
        return $this->multipleOperation('prepend', $ids, $values, $options);
    }

    /**
     * @param string $id
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function remove(string $id, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->remove($id, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param array $ids
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function removeMulti(array $ids, array $options = []): array
    {
        $couchbaseMetaDocs = $this->bucket->remove($ids, $options);

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $couchbaseMetaDoc = new MetaDoc($couchbaseMetaDoc);
        }

        return $couchbaseMetaDocs;
    }

    /**
     * @param string $id
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function get(string $id, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->get($id, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param array $ids
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function getMulti(array $ids, array $options = []): array
    {
        $couchbaseMetaDocs = $this->bucket->get($ids, $options);

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $couchbaseMetaDoc = new MetaDoc($couchbaseMetaDoc);
        }

        return $couchbaseMetaDocs;
    }

    /**
     * @param string $id
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function getFromReplica(string $id, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->getFromReplica($id, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string $id
     * @param int    $expiry
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function getAndTouch(string $id, int $expiry, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->getAndTouch($id, $expiry, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string $id
     * @param int    $expiry
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function touch(string $id, int $expiry, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->touch($id, $expiry, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string $id
     * @param int    $lockTime
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function getAndLock(string $id, int $lockTime, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->getAndLock($id, $lockTime, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string $id
     * @param int    $delta
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function counter(string $id, int $delta, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->counter($id, $delta, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param array $ids
     * @param int   $delta
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function counterMulti(array $ids, int $delta, array $options = []): array
    {
        $couchbaseMetaDocs = $this->bucket->counter($ids, $delta, $options);

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $couchbaseMetaDoc = new MetaDoc($couchbaseMetaDoc);
        }

        return $couchbaseMetaDocs;
    }

    /**
     * @param string $id
     * @param array  $options
     *
     * @return MetaDoc
     * @throws Exception
     */
    public function unlock(string $id, array $options = []): MetaDoc
    {
        try {
            $couchbaseMetaDoc = $this->bucket->unlock($id, $options);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param array $ids
     * @param array $options
     *
     * @return MetaDoc[]
     */
    public function unlockMulti(array $ids, array $options = []): array
    {
        $couchbaseMetaDocs = $this->bucket->unlock($ids, $options);

        foreach ($couchbaseMetaDocs as &$couchbaseMetaDoc) {
            $couchbaseMetaDoc = new MetaDoc($couchbaseMetaDoc);
        }

        return $couchbaseMetaDocs;
    }

    /**
     * @param callable $encoder
     * @param callable $decoder
     */
    public function setTranscoder(callable $encoder, callable $decoder)
    {
        return $this->bucket->setTranscoder($encoder, $decoder);
    }

    /**
     * @param string $query
     *
     * @return Result
     * @throws Exception
     */
    public function query(string $query): Result
    {
        try {
            $couchbaseResult = $this->bucket->query(\CouchbaseN1qlQuery::fromString($query), true);
        } catch (\CouchbaseException $exception) {
            throw ExceptionNormalizer::normalize($exception);
        }

        return new Result($couchbaseResult);
    }

    /**
     * @param array $values
     * @param string $keyPrefix
     *
     * @return array
     */
    private function flatArray(array $values, string $keyPrefix = ''): array
    {
        $data = [];
        foreach ($values as $key => $value) {
            if (is_array($value) === false) {
                $data[$keyPrefix . $key] = $value;
                continue;
            }

            $data = array_replace($data, $this->flatArray($value, $keyPrefix . $key . '.'));
        }

        return $data;
    }

    /**
     * @param string $id
     * @param array $values
     */
    public function update(string $id, array $values)
    {
        $data = $this->flatArray($values);

        /** @var CouchbaseMutateInBuilder $couchbaseMutateInBuilder */
        $couchbaseMutateInBuilder = $this->bucket->mutateIn($id);

        foreach ($data as $key => $value) {
            $couchbaseMutateInBuilder
                ->upsert($key, $value);
        }

        $couchbaseMutateInBuilder
            ->execute();
    }

    /**
     * @param array $values
     *
     * @return MetaDoc[]
     */
    public function save(array $values)
    {
        return $this->insertMulti(array_keys($values), array_values($values));
    }
}
