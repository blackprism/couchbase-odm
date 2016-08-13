<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Trait NotifyPropertyChangedTrait
 */
trait NotifyPropertyChangedTrait
{
    /**
     * @var PropertyChangedListenerInterface[]
     */
    protected $listeners = array();

    /**
     * Add an observer to the current object.
     *
     * @param PropertyChangedListenerInterface $listener
     *
     * @return void
     */
    public function addPropertyChangedListener(PropertyChangedListenerInterface $listener)
    {
        $this->listeners[] = $listener;
    }

    /**
     * Notify all observers with old and new values.
     *
     * @param string $propertyName
     * @param mixed $oldValue
     * @param mixed $newValue
     */
    private function propertyChanged(string $propertyName, $oldValue, $newValue)
    {
        if ($oldValue === $newValue) {
            return;
        }

        foreach ($this->listeners as $listener) {
            if ($this instanceof NotifyPropertyChangedInterface) {
                $listener->propertyChanged($this, $propertyName, $oldValue, $newValue);
            }
        }
    }
}
