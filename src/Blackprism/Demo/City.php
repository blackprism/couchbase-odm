<?php

declare(strict_types = 1);

namespace Blackprism\Demo;

class City
{
    private $name = '';
    private $country = null;

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function countryIs(Country $country)
    {
        $this->country = $country;
    }

    public function getCountry()
    {
        return $this->country;
    }
}

