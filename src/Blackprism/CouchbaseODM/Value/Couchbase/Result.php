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
     * @param array $rows
     * @param string $status
     * @param float $elapsedTime
     * @param float $executionTime
     * @param int $resultCount
     * @param int $resultSize
     */
    public function __construct(
        array $rows,
        string $status,
        float $elapsedTime,
        float $executionTime,
        int $resultCount,
        int $resultSize
    ) {
        $this->rows = $rows;

        if ($status === 'success') {
            $this->success = true;
        }
        $this->elapsedTime = $elapsedTime;
        $this->executionTime = $executionTime;
        $this->resultCount = $resultCount;
        $this->resultSize = $resultSize;
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
