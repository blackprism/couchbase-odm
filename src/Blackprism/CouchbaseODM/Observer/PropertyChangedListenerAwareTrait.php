<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Trait PropertyChangedListenerAwareTrait
 */
trait PropertyChangedListenerAwareTrait
{
    /**
     * @var PropertyChangedListenerInterface
     */
    private $propertyChangedListener;

    /**
     * @param PropertyChangedListenerInterface $propertyChangedListener
     */
    public function propertyChangedListenerIs(PropertyChangedListenerInterface $propertyChangedListener)
    {
        $this->propertyChangedListener = $propertyChangedListener;
    }
}
