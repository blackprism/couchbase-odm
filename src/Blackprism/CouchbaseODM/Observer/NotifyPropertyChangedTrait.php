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
     * Track property changed.
     *
     * @param string $propertyName
     * @param mixed  $oldValue
     * @param mixed  $newValue
     *
     * @return NotifyPropertyChangedInterface
     */
    private function propertyChanged(string $propertyName, $oldValue, $newValue): NotifyPropertyChangedInterface
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

    public function getOriginalPropertiesValue(): array
    {
        return array_column($this->properties, 0);
    }

    public function getCurrentPropertiesValue(): array
    {
        return array_column($this->properties, 1);
    }

    /**
     * @TODO benchmark this
     */
    public function getPropertiesChanged(): array
    {
        return array_filter($this->properties, function ($item) {
            if ($item[0] instanceof NotifyPropertyChangedInterface
                && $item[1] instanceof NotifyPropertyChangedInterface) {
                $propertiesChanged = [];

                $currentPropertiesValue = $item[1]->getCurrentPropertiesValue();
                foreach ($item[0]->getOriginalPropertiesValue() as $index => $propertyValue) {
                    if ($propertyValue !== $currentPropertiesValue[$index]) {
                        $propertiesChanged[] = $currentPropertiesValue[$index];
                    }
                }

                return $propertiesChanged;
            }

            return $item[0] !== $item[1];
        });
    }
}
