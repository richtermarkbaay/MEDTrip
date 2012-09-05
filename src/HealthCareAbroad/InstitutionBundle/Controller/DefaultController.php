<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DefaultController extends InstitutionAwareController
{
	/**
	 * @PreAuthorize("hasAnyRole('INSTITUTION_USER')")
	 *
	 */
    public function indexAction()
    {
        return $this->render('InstitutionBundle:Default:index.html.twig');
    }
    
    public function error403Action()
    {
    	return $this->render('InstitutionBundle:Exception:error403.html.twig');
    }
}
