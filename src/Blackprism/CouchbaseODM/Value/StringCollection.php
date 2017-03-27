<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * StringCollection
 */
class StringCollection implements IteratorAggregate
{

    /**
     * @var ArrayIterator
     */
    private $identifiers;

    /**
     * StringCollection constructor.
     *
     * @param \string[] ...$identifiers
     */
    public function __construct(string ...$identifiers)
    {
        $this->identifiers = new ArrayIterator($identifiers);
    }

    /**
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return $this->identifiers;
    }
}
