<?php

namespace HealthCareAbroad\UserBundle\Event;

use HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation;

class CreateProviderUserEvent extends ProviderUserEvent
{
    private $temporaryPassword;
    
    private $usedInvitation;
    
    /**
     * Set the temporary password created upon creation of account
     * 
     * @param string $password
     * @return CreateProviderUserEvent
     */
    public function setTemporaryPassword($password)
    {
        $this->temporaryPassword = $password;
        
        return $this;
    }
    
    public function getTemporaryPassword($password)
    {
        return $this->temporaryPassword;
    }
    
    /**
     * Set the used ProviderUserInvitation
     * 
     * @param ProviderUserInvitation $invitation
     * @return CreateProviderUserEvent
     */
    public function setUsedInvitation(ProviderUserInvitation $invitation)
    {
        $this->usedInvitation = $invitation;
        
        return $this;
    }
    
    /**
     * @return HealthCareAbroad\ProviderBundle\Entity\ProviderUserInvitation
     */
    public function getUsedInvitation()
    {
        return $this->usedInvitation;
    }
}