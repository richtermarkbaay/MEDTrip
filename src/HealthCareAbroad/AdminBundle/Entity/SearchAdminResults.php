<?php
namespace HealthCareAbroad\AdminBundle\Entity;

class SearchAdminResults
{
    private $id;
    private $firstName;
    private $middleName;
    private $lastName;
    private $name;
    private $description;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->accountId;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }
    
    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName()
    {
        return $this->middleName;
    }

    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this->lastName;
    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
       return "{$this->firstName} {$this->lastName}";
    }
}