<?php

namespace HealthCareAbroad\InstitutionBundle\Exception;

class InstitutionFormException extends \Exception
{
    static public function nonInstitutionFormData($formTypeClass, $data)
    {
        $type = \is_object($data) ? \get_class($data) : \gettype($data);
        
        return new self (sprintf('Form %s expects instance of Institution as data %s', $formTypeClass, $type));
    }
}