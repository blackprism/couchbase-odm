<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * Trait NotifyPropertyChangedTrait
 */
trait NotifyPropertyChangedTrait
{

    private $track = false;
    private $properties = array();

    // @TODO revoit le nom de la méthode/propriété
    public function track()
    {
        $this->track = true;
    }

    /**
     * @return bool
     */
    public function isTracked()
    {
        return $this->track;
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
        if ($this->track === false) {
            return;
        }

        $this->properties[$propertyName] = 1;
    }

    /**
     * @return array
     */
    public function getPropertiesChanged()
    {
        return $this->properties;
    }
}
