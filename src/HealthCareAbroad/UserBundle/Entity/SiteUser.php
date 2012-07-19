<?php
namespace HealthCareAbroad\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class SiteUser implements UserInterface
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    
    protected $email;
    protected $password;
    protected $firstName;
    protected $middleName;
    protected $lastName;
    protected $accountToken;
    protected $roles=array();
    
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
    
    /**
     * UserInterface
     */
    
    public function getPassword()
    {
        return $this->password;
    }
    
    public function getSalt()
    {
        return $this->accountToken;
    }
    
    public function getUsername()
    {
        return $this->email;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }
    
    public function eraseCredentials()
    {
        
    }
    
    public function __toString()
    {
        return "{$this->firstName} {$this->lastName}";
    }
    
}