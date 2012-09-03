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
}
