<?php

namespace HealthCareAbroad\MemcacheBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MemcacheBundle:Default:index.html.twig', array('name' => $name));
    }
}
