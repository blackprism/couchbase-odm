<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedTrait;

class Country implements NotifyPropertyChangedInterface
{
    use NotifyPropertyChangedTrait;

    private $name = '';
    private $geo = null;

    public function setName(string $name)
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setGeo(Geo $geo)
    {
        $geo = clone $geo;
        $this->propertyChanged('geo', $this->geo, $geo);
        $this->geo = $geo;
    }

    public function getGeo()
    {
        return $this->geo;
    }
}
