<?php
/**
 * Constraint for user name field that should have unique value
 * 
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class InstitutionUserNameValue extends Constraint
{
 	public $message = 'Institution name already exists.';
 	
    public $field ;

    public function getRequiredOptions()
    {
        return array('field');
    }
    
    public function validatedBy()
    {
    	return 'helper.institution_name.validator';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function getDefaultOption()
    {
        return 'field';
    }

}