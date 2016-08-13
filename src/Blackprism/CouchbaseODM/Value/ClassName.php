<?php

declare(strict_types=1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * ClassName
 */
final class ClassName
{

    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $className)
    {
        if (class_exists($className) === false) {
            throw new \InvalidArgumentException($className . ' not found');
        }

        $this->className = $className;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->className;
    }
}
