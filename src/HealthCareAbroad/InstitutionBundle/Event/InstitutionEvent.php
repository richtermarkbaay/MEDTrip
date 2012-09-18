<?php 
/**
 * Event class for Institution
 * 
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Event;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Event\BaseEvent;

class InstitutionEvent extends BaseEvent
{
    /**
     * @return Institution
     */
    public function getInstitution()
    {
        return $this->data;
    }
}