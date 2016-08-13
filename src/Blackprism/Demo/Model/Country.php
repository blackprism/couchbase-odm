<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

class Country
{
    private $name = '';

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
