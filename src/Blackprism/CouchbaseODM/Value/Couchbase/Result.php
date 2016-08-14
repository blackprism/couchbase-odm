<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value\Couchbase;

/**
 * Result
 */
final class Result
{
    /**
     * @var array
     */
    private $rows;

    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var float
     */
    private $elapsedTime;

    /**
     * @var float
     */
    private $executionTime;

    /**
     * @var int
     */
    private $resultCount;

    /**
     * @var int
     */
    private $resultSize;

    /**
     * Result constructor.
     *
     * @param \stdClass $couchbaseResult
     */
    public function __construct($couchbaseResult)
    {
        if ($couchbaseResult->status === 'success') {
            $this->success = true;
        }

        $this->rows = $couchbaseResult->rows;
        $this->elapsedTime = (float) $couchbaseResult->metrics['elapsedTime'];
        $this->executionTime = (float) $couchbaseResult->metrics['executionTime'];
        $this->resultCount = $couchbaseResult->metrics['resultCount'];
        $this->resultSize = $couchbaseResult->metrics['resultSize'];
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        return $this->rows;
    }

    /**
     * @return bool
     */
    public function success(): bool
    {
        return $this->success;
    }

    /**
     * @return float
     */
    public function elapsedTime(): float
    {
        return $this->elapsedTime;
    }

    /**
     * @return float
     */
    public function executionTime(): float
    {
        return $this->executionTime;
    }

    /**
     * @return int
     */
    public function resultCount(): int
    {
        return $this->resultCount;
    }

    /**
     * @return int
     */
    public function resultSize(): int
    {
        return $this->resultSize;
    }
}
