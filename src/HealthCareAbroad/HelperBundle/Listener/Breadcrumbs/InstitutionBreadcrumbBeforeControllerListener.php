<?php
namespace HealthCareAbroad\HelperBundle\Listener\Breadcrumbs;

use HealthCareAbroad\HelperBundle\Listener\Breadcrumbs\BreadcrumbBeforeControllerListener;

class InstitutionBreadcrumbBeforeControllerListener extends BreadcrumbBeforeControllerListener
{
    protected $templateName='InstitutionBundle:Default:breadcrumbs.html.twig';
    
    /**
     * (non-PHPdoc)
     * @see HealthCareAbroad\HelperBundle\Listener\Breadcrumbs.BreadcrumbBeforeControllerListener::validate()
     */
    protected function validate()
    {
        // this check is only based on convention that all admin routes start with admin
        if (!\preg_match('/^institution/', $this->matchedRoute)) {
            // not a client admin route
            return false;
        }
    
        return false;
    }
}
