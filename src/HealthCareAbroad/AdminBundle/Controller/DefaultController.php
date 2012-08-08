<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller
{
    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }
    
    public function settingsAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
}
