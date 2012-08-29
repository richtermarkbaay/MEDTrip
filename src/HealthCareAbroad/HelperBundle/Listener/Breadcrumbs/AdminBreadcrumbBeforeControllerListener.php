<?php

namespace HealthCareAbroad\HelperBundle\Listener\Breadcrumbs;

use HealthCareAbroad\HelperBundle\Listener\Breadcrumbs\BreadcrumbBeforeControllerListener;

class AdminBreadcrumbBeforeControllerListener extends BreadcrumbBeforeControllerListener
{
    protected $templateName='AdminBundle:Default:breadcrumbs.html.twig';
    
    /**
     * (non-PHPdoc)
     * @see HealthCareAbroad\HelperBundle\Listener\Breadcrumbs.BreadcrumbBeforeControllerListener::validate()
     */
    protected function validate()
    {
        // this check is only based on convention that all admin routes start with admin
        if (!\preg_match('/^admin/', $this->matchedRoute)) {
            // non-admin route
            return false;
        }
        
        return true;
    }
}