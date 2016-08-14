<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM;

use Blackprism\CouchbaseODM\Value\Couchbase\MetaDoc;
use Blackprism\CouchbaseODM\Value\Couchbase\Result;
use Blackprism\CouchbaseODM\Value\DocumentId;

/**
 * Bucket
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
     * @param DocumentId $id
     *
     * @return MetaDoc
     */
    public function get(DocumentId $id): MetaDoc
    {
        $couchbaseMetaDoc = $this->bucket->get($id->value());

        return new MetaDoc($couchbaseMetaDoc);
    }

    /**
     * @param string $query
     *
     * @return Result
     */
    public function query(string $query): Result
    {
        $couchbaseResult = $this->bucket->query(\CouchbaseN1qlQuery::fromString($query), true);

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
     * @param DocumentId $id
     * @param array $values
     */
    public function update(DocumentId $id, array $values)
    {

        $data = $this->flatArray($values);

        /** @var CouchbaseMutateInBuilder $couchbaseMutateInBuilder */
        $couchbaseMutateInBuilder = $this->bucket->mutateIn($id->value());

        foreach ($data as $key => $value) {
            $couchbaseMutateInBuilder
                ->upsert($key, $value);
        }
        $couchbaseMutateInBuilder
            ->execute();
    }
}
