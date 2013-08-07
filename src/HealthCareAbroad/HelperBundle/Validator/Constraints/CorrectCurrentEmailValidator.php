<?php
/**
 * Validator for CorrectCurrentEmail constraint
 * 
 * @author Chaztine Blance
 *
 */

namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use ChromediaUtilities\Helpers\SecurityHelper;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;

class CorrectCurrentEmailValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $valid = $value == $constraint->siteUser->getEmail(); // compare the value of the email with the siteUser's email
        if (!$valid) {
            $this->context->addViolation($constraint->message);
            return;
        }
    }
}