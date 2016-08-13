<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Interface NotifyPropertyChangedInterface
 */
interface NotifyPropertyChangedInterface
{
    /**
     * Add an observer to the current object.
     *
     * @param PropertyChangedListenerInterface $listener
     *
     * @return mixed
     */
    public function addPropertyChangedListener(PropertyChangedListenerInterface $listener);
}
