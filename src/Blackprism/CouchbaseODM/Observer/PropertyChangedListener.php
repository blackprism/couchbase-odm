<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * PropertyChangedListener
 */
class PropertyChangedListener implements PropertyChangedListenerInterface
{

    const PROPERTY_NAME = 'couchbaseODM_UUID';

    private $documents = []; /* @TODO private, protected ? */

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
    ) {
        if ($oldValue === $newValue) {
            return;
        }

        if (isset($document->{self::PROPERTY_NAME}) === false) {
            $document->{self::PROPERTY_NAME} = $this->generateUUID();
        }

        if (isset($this->documents[$document->{self::PROPERTY_NAME}][$propertyName]) === false) {
            $this->documents[$document->{self::PROPERTY_NAME}][$propertyName][0] = $oldValue;
        }

        $this->documents[$document->{self::PROPERTY_NAME}][$propertyName][1] = $newValue;
    }

    /**
     * @param NotifyPropertyChangedInterface $document
     *
     * @return array
     */
    public function getPropertiesChanged(NotifyPropertyChangedInterface $document): array
    {
        if (isset($document->{self::PROPERTY_NAME}) === false) {
            return [];
        }

        $properties = [];
        foreach ($this->documents[$document->{self::PROPERTY_NAME}] as $propertyName => $propertyValues) {
            if ($propertyValues[0] !== $propertyValues[1]) {
                $properties[] = $propertyName;
            }
        }

        return $properties;
    }

    /**
     * @return string
     */
    protected function generateUUID(): string /* @TODO private, protected ? */
    {
        return uniqid(rand(), true);
    }
}
