<?php

namespace HealthCareAbroad\SearchBundle\Services\Admin;

class AdminSearchResult
{
    private $id;
    private $firstName;
    private $middleName;
    private $lastName;
    private $name;
    private $description;
    private $url;
    
	public function getId()
	{
	    return $this->id;
	}
	public function setId($id)
	{
	    $this->id = $id;
	}
	
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getUrl()
    {
        return $this->url;
    }
    
    public function setUrl($url)
    {
        $this->url = $url;
    }
    
    public function getDescription()
    {
    	return $this->description;
    }
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
    public function getFirstName()
    {
        return $firstName;
    }
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }
    public function getLastName()
    {
        return $lastName;
    }
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }
    public function getMiddleName()
    {
        return $this->middleName;
    }
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
    }
    public function getFullName()
    {
    	return "{$this->firstName} {$this->lastName}";
    }
}