<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

class Flatten
{
    private $city = '';
    private $country = '';

    public function setCity(string $city)
    {
        $this->city = $city;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCountry(string $country)
    {
        $this->country = $country;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}

