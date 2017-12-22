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
    private $class = '';

    /**
     * @var array
     */
    private $propertyType = [];

    /**
     * @var array
     */
    private $properties = [];

    public function classIs(string $class): IsMapping
    {
        $this->class = $class;
        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function propertyTypeIs(string $property, string $value): IsMapping
    {
        $this->propertyType = [
            'property' => $property,
            'value'    => $value
        ];

        return $this;
    }

    public function getPropertyType(): array
    {
        return $this->propertyType;
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
