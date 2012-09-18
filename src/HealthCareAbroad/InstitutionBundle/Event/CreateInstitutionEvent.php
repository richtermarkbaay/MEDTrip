<?php
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\UserBundle\Entity\InstitutionUser;

class CreateInstitutionEvent extends InstitutionEvent
{
    /**
     * @return InstitutionUser
     */
    public function getInstitutionUser()
    {
        return $this->getOption('institutionUser');
    }
}