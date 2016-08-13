<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * DocumentId
 */
final class DocumentId
{
    /**
     * @var string
     */
    private $id;

    /**
     * @param string $id
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $id)
    {
        if (mb_check_encoding($id, 'UTF-8') === false) {
            throw new \InvalidArgumentException($id . ' is not a valid UTF-8 encoding');
        }

        if (mb_strlen($id, 'ASCII') > 250) {
            throw new \InvalidArgumentException($id . ' max length is 250 bytes');
        }

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->id;
    }
}
