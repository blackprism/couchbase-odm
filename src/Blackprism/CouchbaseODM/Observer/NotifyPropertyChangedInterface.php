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

    /**
     * Get properties changed
     *
     * @return array
     */
    public function getPropertiesChanged(): array;

    /**
     * Get original properties value
     *
     * @return array
     */
    public function getOriginalPropertiesValue(): array;

    /**
     * Get current properties value
     *
     * @return array
     */
    public function getCurrentPropertiesValue(): array;
}
