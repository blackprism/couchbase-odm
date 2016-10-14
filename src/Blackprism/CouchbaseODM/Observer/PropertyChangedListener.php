<?php

namespace Blackprism\CouchbaseODM\Observer;

/**
 * PropertyChangedListener
 * @TODO à cause du isNew on se rend vraiment compte que cette classe est plus un UOW, il faudra la renommer
 */
class PropertyChangedListener implements PropertyChangedListenerInterface
{

    // @TODO on a plus besoin d'un uuid vu que les metas de changements sont stockées dans chaque objet
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
     * @TODO à ajouter dans l'interface UOW
     *
     * @param NotifyPropertyChangedInterface $document
     *
     * @return bool
     */
    public function isNew(NotifyPropertyChangedInterface $document): bool
    {
        if (isset($document->{self::PROPERTY_NAME}) === true) {
            return false;
        }

        return true;
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
        return uniqid(mt_rand(), true);
    }
}
