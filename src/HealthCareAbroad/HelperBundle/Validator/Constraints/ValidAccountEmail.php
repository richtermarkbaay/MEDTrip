<?php
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidAccountEmail extends Constraint
{
    public $message = "This is not a valid email";
}