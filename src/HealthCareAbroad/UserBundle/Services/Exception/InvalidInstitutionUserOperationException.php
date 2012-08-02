<?php
namespace HealthCareAbroad\UserBundle\Services\Exception;

use \Exception;

class InvalidInstitutionUserOperationException extends \Exception
{
    public static function illegalUpdateWithNoAccountId()
    {
        return new self('Cannot update an InstitutionUser without an accountId.');
    }
}