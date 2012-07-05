<?php

namespace HealthCareAbroad\HelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('HelperBundle:Default:index.html.twig', array('name' => $name));
    }
}
