<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Repository;

use RuntimeException;

/**
 * Mapping
 */
final class Mapping implements IsMapping
{
    /**
     * @var string
     */
    private $root = '';

    /**
     * @var string
     */
    private $class = '';

    /**
     * @var array
     */
    private $properties = [];

    public function rootIs(string $root): IsMapping
    {
        $this->root = $root;
        return $this;
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    public function classIs(string $class): IsMapping
    {
        $this->class = $class;
        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function propertyHasAccessors(string $property, string $setterMethod, string $getterMethod): IsMapping
    {
        $this->properties[$property] = [
            'setter' => $setterMethod,
            'getter' => $getterMethod
        ];

        return $this;
    }

    public function propertyHasMappingAndAccessors(
        string $property,
        IsMapping $mapping,
        string $setterMethod,
        string $getterMethod
    ): IsMapping {
        $this->properties[$property] = [
            'mapping' => $mapping,
            'setter'  => $setterMethod,
            'getter'  => $getterMethod
        ];

        return $this;
    }

    public function hasProperty(string $property): bool
    {
        return isset($this->properties[$property]);
    }

    public function propertyHasMapping(string $property): bool
    {
        if (isset($this->properties[$property]) === false) {
            throw new RuntimeException("No such property $property");
        }

        return isset($this->properties[$property]['mapping']);
    }

    public function getPropertyMapping(string $property): IsMapping
    {
        if (isset($this->properties[$property]) === false) {
            throw new RuntimeException("No such property $property");
        }

        return $this->properties[$property]['mapping'];
    }

    public function getPropertySetter(string $property): string
    {
        if (isset($this->properties[$property]) === false) {
            throw new RuntimeException("No such property $property");
        }

        return $this->properties[$property]['setter'];
    }

    public function getPropertyGetter(string $property): string
    {
        if (isset($this->properties[$property]) === false) {
            throw new RuntimeException("No such property $property");
        }

        return $this->properties[$property]['getter'];
    }
}
