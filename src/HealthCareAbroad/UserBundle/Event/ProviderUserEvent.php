<?php
namespace HealthCareAbroad\UserBundle\Event;

use HealthCareAbroad\UserBundle\Entity\ProviderUser;

use Symfony\Component\EventDispatcher\Event;

abstract class ProviderUserEvent extends Event
{
    protected $providerUser;
    
    
    public function __construct(ProviderUser $user)
    {
        $this->providerUser = $user;
    }
    
    public function getProviderUser()
    {
        return $this->providerUser;
    }
}