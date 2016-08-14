<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedTrait;

class City implements NotifyPropertyChangedInterface
{

    use NotifyPropertyChangedTrait;

    private $id = null;
    private $name = '';
    private $country = null;
    private $geo = null;
    private $mayorId = null;
    private $mayor = null;

    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function countryIs(Country $country)
    {
        $this->propertyChanged('country', $this->country, $country);
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setGeo(Geo $geo)
    {
        $this->propertyChanged('geo', $this->geo, $geo);
        $this->geo = $geo;
    }

    public function getGeo()
    {
        return clone $this->geo;
    }

    public function setMayor(Mayor $mayor)
    {
        $this->mayor = $mayor;
    }

    public function getMayor()
    {
        return clone $this->mayor;
    }

    public function setMayorId(string $mayorId)
    {
        $this->propertyChanged('mayorId', $this->mayorId, $mayorId);
        $this->mayorId = $mayorId;
    }

    public function getMayorId()
    {
        return $this->mayorId;
    }
}

