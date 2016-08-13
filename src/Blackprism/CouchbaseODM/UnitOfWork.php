<?php

namespace Blackprism\CouchbaseODM;

use Blackprism\CouchbaseODM\Observer\PropertyChangedListenerInterface;

/**
 * UnitOfWork
 */
final class UnitOfWork
{
    /**
     * @var PropertyChangedListenerInterface
     */
    private $propertyChangedListener;

    public function __construct(PropertyChangedListenerInterface $propertyChangedListener)
    {
        $this->propertyChangedListener = $propertyChangedListener;
    }
}
