<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

class FilterByKey extends \FilterIterator
{
    /**
     * @var mixed
     */
    private $key = null;

    /**
     * @param mixed $key
     */
    public function keyIs($key)
    {
        $this->key = $key;
    }

    /**
     * @return bool
     */
    public function accept()
    {
        if ($this->key === null) {
            return true;
        }

        /**
         * Not a strict comparison
         */
        return $this->key() == $this->key;
    }
}
