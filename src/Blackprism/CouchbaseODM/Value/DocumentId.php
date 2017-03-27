<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * DocumentId
 * @TODO à supprimer trop lent
 */
final class DocumentId
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param string $identifier
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $identifier)
    {
        if (mb_check_encoding($identifier, 'UTF-8') === false) {
            throw new \InvalidArgumentException($identifier . ' is not a valid UTF-8 encoding');
        }

        if (mb_strlen($identifier, 'ASCII') > 250) {
            throw new \InvalidArgumentException($identifier . ' max length is 250 bytes');
        }

        $this->identifier = $identifier;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->identifier;
    }
}
