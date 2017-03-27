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
    public function isTracked(): bool
    {
        return $this->track;
    }

    /**
     * Notify all observers with old and new values.
     *
     * @param string $propertyName
     * @param mixed  $oldValue
     * @param mixed  $newValue
     *
     * @return self
     */
    private function propertyChanged(string $propertyName, $oldValue, $newValue): self
    {
        if ($this->isTracked() === false) {
            return $this;
        }

        if (isset($this->properties[$propertyName]) === false) {
            $this->properties[$propertyName] = [$oldValue, $newValue];
            return $this;
        }

        if ($this->properties[$propertyName][0] !== $newValue) {
            $this->properties[$propertyName][1] = $newValue;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPropertiesChanged(): array
    {
        return array_filter($this->properties, function ($item) {
            return $item[0] !== $item[1];
        });
    }
}
