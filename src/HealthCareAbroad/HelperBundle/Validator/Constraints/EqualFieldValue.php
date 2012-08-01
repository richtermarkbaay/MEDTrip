<?php
/**
 * Constraint for two fields that should have equal value
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class EqualFieldValue extends Constraint
{
    public $message = 'This value does not equal the value of comparison field';
    public $field;
    
    /**
     * {@inheritDoc}
     */
    public function getDefaultOption()
    {
        return 'field';
    }
    
    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('field');
    }
}