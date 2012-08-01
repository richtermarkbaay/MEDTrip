<?php
/**
 * Validator for EqualFieldValue 
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;

class EqualFieldValueValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $valid = $value == $this->context->getRoot()->get($constraint->field)->getData();
        
        if (!$valid) {
            $this->context->addViolation($constraint->message, array('{{ field }}' => $value));
            return;
        }
    }
}