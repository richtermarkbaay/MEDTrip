<?php
namespace HealthCareAbroad\UserBundle\Entity;

abstract class SiteUser 
{
    protected $email;
    protected $password;
    protected $firstName;
    protected $middleName;
    protected $lastName;
    
    /**
     * @var bigint $accountId
     */
    protected $accountId;
    
    /**
     * Set account id
     * 
     * @param bigint $accountId
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;
    }
    
    /**
     * Get accountId
     *
     * @return bigint
     */
    public function getAccountId()
    {
        return $this->accountId;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
        
        return $this;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function setPassword($password)
    {
        $this->password = $password;
        
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
    
}