<?php
namespace HealthCareAbroad\UserBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

abstract class SiteUser implements UserInterface, \Serializable
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    protected $id;
    protected $email;
    protected $password;
    protected $firstName;
    protected $middleName;
    protected $lastName;
    protected $contactNumber;
    protected $accountToken;
    protected $roles = array();

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
        $this->id = $accountId;
    }

    public function setId($id)
    {
        $this->id = $id;
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

    /**
     * Alias for getAccountId
     *
     * @return bigint
     */
    public function getId()
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
    
    public function getContactNumber()
    {
        return $this->contactNumber;
    }
    
    public function setContactNumber($contactNumber)
    {
        $this->contactNumber = $contactNumber;
    
        return $this->contactNumber;
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

    public function setRoles(array $roles)
    {
        $this->roles = $roles;
    }

    public function eraseCredentials()
    {

    }

    public function __toString()
    {
        return $this->getFullName();
    }

    public function getFullName()
    {
       return "{$this->firstName} {$this->lastName}";
    }

    public function serialize()
    {
        return implode(',', array(
                        'id' => $this->getId(),
                        'email' => $this->getEmail(),
                        'password'    => $this->getPassword(),
                        'firstName' => $this->getFirstName(),
                        'middleName' => $this->getMiddleName(),
                        'contactNumber' => $this->getContactNumber(),
                        'lastName' => $this->getLastName(),
                        'accountToken' => $this->accountToken,
                        'roles' => implode(',', $this->getRoles())
        ));
    }

    public function unserialize($strSerialized)
    {
        $serialized = explode(',', $strSerialized);

        $this->setId($serialized[0]);
        $this->setEmail($serialized[1]);
        $this->setPassword($serialized[2]);
        $this->setFirstName($serialized[3]);
        $this->setMiddleName($serialized[4]);
        $this->setLastName($serialized[5]);
        $this->contactNumber = $serialized[6];
        $this->accountToken = $serialized[7];
        $this->setRoles(explode(',', $serialized[8]));
    }

}