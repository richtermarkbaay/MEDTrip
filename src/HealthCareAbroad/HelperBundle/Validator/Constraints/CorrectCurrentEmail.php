<?php
/**
 * Constraint for correct email field for a SiteUser
 * 
 * Required options are:
 *     - siteUser
 * 
 * @author Chaztine Blance
 *
 */
namespace HealthCareAbroad\HelperBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CorrectCurrentEmail extends Constraint
{
    public $message = "Incorrect email address.";
    
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