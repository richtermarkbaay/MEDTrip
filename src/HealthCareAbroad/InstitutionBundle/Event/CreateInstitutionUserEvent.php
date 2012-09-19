<?php

namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionUserEvent;

/**
 * Event class for creating a new user event
 * 
 * Event options are:
 *     - temporaryPassword
 *     - usedInvitation
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class CreateInstitutionUserEvent extends InstitutionUserEvent
{
    const OPTION_TEMPORARY_PASSWORD = 'temporaryPassword';
    
    const OPTION_USED_INVITATION = 'usedInvitation';
    
    public function getTemporaryPassword()
    {
        return $this->getOption(self::OPTION_TEMPORARY_PASSWORD);
    }
    
    public function getUsedInvitation()
    {
        return $this->getOption(self::OPTION_USED_INVITATION);
    }
}