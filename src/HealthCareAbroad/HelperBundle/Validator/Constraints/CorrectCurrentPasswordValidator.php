<?php
/**
 * Validator for CorrectCurrentPassword constraint
 * 
 * @author Allejo Chris G. Velarde
 *
 */

namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use ChromediaUtilities\Helpers\SecurityHelper;

use Symfony\Component\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;

class CorrectCurrentPasswordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $value = SecurityHelper::hash_sha256($value); // hash the value of the password in question
        $valid = $value == $constraint->siteUser->getPassword(); // compare the value of the password with the siteUser's password
        if (!$valid) {
            $this->context->addViolation($constraint->message);
            return;
        }
    }
}