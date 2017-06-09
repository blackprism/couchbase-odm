<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Repository;

use RuntimeException;

/**
 * Interface IsMapping
 */
interface IsMapping
{
    /**
     * @param string $root
     *
     * @return self
     */
    public function rootIs(string $root): self;

    /**
     *
     * @return string
     */
    public function getRoot(): string;

    /**
     * @param string $class
     *
     * @return self
     */
    public function classIs(string $class): self;

    /**
     *
     * @return string
     */
    public function getClass(): string;

    /**
     * @param string $property
     * @param string $setterMethod
     * @param string $getterMethod
     *
     * @return self
     */
    public function propertyHasAccessors(string $property, string $setterMethod, string $getterMethod): self;

    /**
     * @param string $property
     * @param self $mapping
     * @param string $setterMethod
     * @param string $getterMethod
     *
     * @return self
     */
    public function propertyHasMappingAndAccessors(
        string $property,
        self $mapping,
        string $setterMethod,
        string $getterMethod
    ): self;

    /**
     * @param string $property
     *
     * @return bool
     */
    public function hasProperty(string $property): bool;

    /**
     * @param string $property
     *
     * @todo Mettre une exception interne
     * @throws RuntimeException
     *
     * @return bool
     */
    public function propertyHasMapping(string $property): bool;

    /**
     * @param string $property
     *
     * @todo Mettre une exception interne
     * @throws RuntimeException
     *
     * @return self
     */
    public function getPropertyMapping(string $property): self;

    /**
     * @param string $property
     *
     * @todo Mettre une exception interne
     * @throws RuntimeException
     *
     * @return string
     */
    public function getPropertySetter(string $property): string;

    /**
     * @param string $property
     *
     * @todo Mettre une exception interne
     * @throws RuntimeException
     *
     * @return string
     */
    public function getPropertyGetter(string $property): string;
}
