<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Denormalizer;

class KeepComposedKey extends \FilterIterator
{
    /**
     * @return bool
     */
    public function accept()
    {
        if (is_string($this->key()) === false) {
            return false;
        }

        return strpos($this->key(), '.') !== false;
    }
}
