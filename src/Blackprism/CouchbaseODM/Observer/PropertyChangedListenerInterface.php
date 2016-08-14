<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Interface PropertyChangedListenerInterface
 */
interface PropertyChangedListenerInterface
{
    /**
     * Notify all observers with old and new values.
     *
     * @param NotifyPropertyChangedInterface $document
     * @param string $propertyName
     * @param mixed $oldValue
     * @param mixed $newValue
     *
     * @return void
     */
    public function propertyChanged(
        NotifyPropertyChangedInterface $document,
        string $propertyName,
        $oldValue,
        $newValue
    );

    /**
     * @param NotifyPropertyChangedInterface $document
     *
     * @return array
     */
    public function getPropertiesChanged(NotifyPropertyChangedInterface $document): array;
}
