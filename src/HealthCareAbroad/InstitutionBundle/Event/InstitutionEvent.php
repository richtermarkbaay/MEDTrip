<?php 
namespace HealthCareAbroad\InstitutionBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;

class InstitutionEvent extends Event
{
    protected $order;

    public function __construct(Institution $institution)
    {
        $this->institution = $institution;
    }

    public function getInstitution()
    {
        return $this->institution;
    }
}