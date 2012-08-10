<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DefaultController extends Controller
{
    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function settingsAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
    
    public function error403Action()
    {
        return $this->render('AdminBundle:Default:error403.html.twig');
    }
}
