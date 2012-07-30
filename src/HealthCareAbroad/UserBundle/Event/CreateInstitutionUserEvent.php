<?php

namespace HealthCareAbroad\UserBundle\Event;

use HealthCareAbroad\ProviderBundle\Entity\InstitutionUserInvitation;

class CreateInstitutionUserEvent extends InstitutionUserEvent
{
    private $temporaryPassword;
    
    private $usedInvitation;
    
    /**
     * Set the temporary password created upon creation of account
     * 
     * @param string $password
     * @return CreateInstitutionUserEvent
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
     * Set the used InstitutionUserInvitation
     * 
     * @param InstitutionUserInvitation $invitation
     * @return CreateInstitutionUserEvent
     */
    public function setUsedInvitation(InstitutionUserInvitation $invitation)
    {
        $this->usedInvitation = $invitation;
        
        return $this;
    }
    
    /**
     * @return HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation
     */
    public function getUsedInvitation()
    {
        return $this->usedInvitation;
    }
}