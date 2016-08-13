<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

class Geo
{
    private $lat = 0;
    private $lon = 0;

    public function setLat(float $lat)
    {
        $this->lat = $lat;
    }

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLon(float $lon)
    {
        $this->lon = $lon;
    }

    public function getLon(): float
    {
        return $this->lon;
    }
}

