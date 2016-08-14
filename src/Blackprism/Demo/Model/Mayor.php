<?php

declare(strict_types = 1);

namespace Blackprism\Demo\Model;

use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedInterface;
use Blackprism\CouchbaseODM\Observer\NotifyPropertyChangedTrait;

class Mayor implements NotifyPropertyChangedInterface
{
    use NotifyPropertyChangedTrait;

    private $id;
    private $firstname = '';
    private $lastname = '';

    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFirstname(string $firstname)
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setLastname(string $lastname)
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }
}

