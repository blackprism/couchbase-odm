<?php

declare(strict_types = 1);

namespace Blackprism\CouchbaseODM\Serializer\Decoder;

class KeepComposedKey extends \FilterIterator
{
    /**
     * @return bool
     */
    public function accept()
    {
        return strpos($this->key(), '.') !== false;
    }
}
