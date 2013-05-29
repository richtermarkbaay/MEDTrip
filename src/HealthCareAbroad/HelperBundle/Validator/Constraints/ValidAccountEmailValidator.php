<?php

namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;

use Symfony\Component\Validator\Constraints\EmailValidator;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\UserBundle\Entity\SiteUser;
use HealthCareAbroad\UserBundle\Services\UserService;

class ValidAccountEmailValidator extends EmailValidator
{
    /**
     * @var Registry
     */
    private  $doctrine;
	
    /**
     * @var UserService
     */
    private $service;
    
    public function setUserService (UserService $service)
    {
	    $this->service = $service;
    }
	
	public function validate($value, Constraint $constraint)
    {
        $currentViolationsCount = \count($this->context->getViolations());
        $currentMessage = $constraint->message;
        $constraint->message = $constraint->invalidEmailMessage;  // switch first to invalidEmailMessage
        
        // do validation of EmailValidator 
        parent::validate($value, $constraint);
        
        // there was violation in EmailValidator
        if (\count($this->context->getViolations()) > $currentViolationsCount ){
            return;
        }
        
        $constraint->message = $currentMessage; // switch to original constraint message
        if($constraint->currentAccountEmail != $value){ // if email set is same as the current email do nothing
            $user = $this->service->find(array('email'=> $value), array());
            
            if (!$user) {
                return;
            }
            
            $this->context->addViolation($constraint->message, array('{{ field }}' => $value));
        }
        
        return;
    }
}