<?php

namespace HealthCareAbroad\HelperBundle\Event;
use Symfony\Component\EventDispatcher\Event;
use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

class CreateErrorReportEvent extends event
{
    private $errorReport;
    
    public function __construct(ErrorReport $errorReport)
    {
    	$this->errorReport = $errorReport;
    }
    
    public function getErrorReport()
    {
    	return $this->errorReport;
    }
}