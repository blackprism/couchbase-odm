<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM;

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
     * @return mixed
     */
    public function get(DocumentId $id)
    {
        /* @TODO need to be rewrite
        $couchbaseMetaDoc = $this->bucket->get($id->value());

        return new MetaDoc(
            $couchbaseResult->rows,
            $couchbaseResult->status,
            (float) $couchbaseResult->metrics['elapsedTime'],
            (float) $couchbaseResult->metrics['executionTime'],
            $couchbaseResult->metrics['resultCount'],
            $couchbaseResult->metrics['resultSize']
        );

        var_dump($couchbaseMetaDoc);
        die;
        $document = $couchbaseMetaDoc->value;

        return $this->deserializer->deserialize($document);
        */
    }

    /**
     * @param string $query
     *
     * @return Result
     */
    public function query(string $query)
    {
        $couchbaseResult = $this->bucket->query(\CouchbaseN1qlQuery::fromString($query), true);

        return new Result(
            $couchbaseResult->rows,
            $couchbaseResult->status,
            (float) $couchbaseResult->metrics['elapsedTime'],
            (float) $couchbaseResult->metrics['executionTime'],
            $couchbaseResult->metrics['resultCount'],
            $couchbaseResult->metrics['resultSize']
        );
    }

    private function flatArray(array $values, $keyPrefix = '')
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
