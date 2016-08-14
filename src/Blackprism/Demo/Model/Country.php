<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedTrait;

class Country implements NotifyPropertyChangedInterface
{
    use NotifyPropertyChangedTrait;

    private $name = '';

    public function setName(string $name)
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
