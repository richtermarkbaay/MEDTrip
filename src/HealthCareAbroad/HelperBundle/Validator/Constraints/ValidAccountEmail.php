<?php

/*
 * @author Chaztine Blance
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidAccountEmail extends Constraint
{
    public $message = "This is not a valid email";
    public $checkMX = false;
    public $checkHost = false;
    public $field;
    public $serviceValidator ='services.user';
    

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
    	
    	return array('field');
    }
}