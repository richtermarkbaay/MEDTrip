<?php
/**
 * Constraint for correct password field for a SiteUser
 * 
 * Required options are:
 *     - siteUser
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CorrectCurrentPassword extends Constraint
{
    public $message = "Incorrect password.";
    
    /**
     * @var HealthCareAbroad\UserBundle\Entity\SiteUser
     */
    public $siteUser; 
    
    /**
     * {@inheritDoc}
     */
    public function getRequiredOptions()
    {
        return array('siteUser');
    }
}