<?php

/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidAccountEmail extends Constraint
{
    public $message = "Email already exists.";
    public $invalidEmailMessage = "Please provide a valid email";
    public $checkMX = false;
    public $checkHost = false;
    public $field;
    public $currentAccountEmail;

    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
    	return 'field';
    }
    
    
    public function validatedBy()
    {
 
    	return 'helper.account_email.validator';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
    	return self::CLASS_CONSTRAINT;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    { 
    	
    	return array('field','currentAccountEmail');
    }
}