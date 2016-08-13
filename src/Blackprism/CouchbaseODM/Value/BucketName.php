<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Value;

/**
 * BucketName
 *
 * @property string $name
 */
final class BucketName
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(string $name)
    {
        if (preg_match('@^[a-zA-Z0-9_./%]{1,100}$@', $name) === 1) {
            throw new \InvalidArgumentException(
                'Your bucket name can only contain characters in range A-Z, a-z, 0-9 as well as underscore, period, '
                . 'dash and percent and can be a maximum of 100 characters in length.'
            );
        }

        $this->name = $name;
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->name;
    }
}
