<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Interface PropertyChangedListenerAware
 */
interface PropertyChangedListenerAwareInterface
{
    /**
     * @param PropertyChangedListenerInterface $propertyChangedListener
     */
    public function propertyChangedListenerIs(PropertyChangedListenerInterface $propertyChangedListener);
}
