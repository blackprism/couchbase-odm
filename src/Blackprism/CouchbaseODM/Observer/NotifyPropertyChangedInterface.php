<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Interface NotifyPropertyChangedInterface
 */
interface NotifyPropertyChangedInterface
{
    /**
     * Enable tracking on object.
     */
    public function track();

    /**
     * Check if object is tracked.
     *
     * @return bool
     */
    public function isTracked(): bool;
}
