<?php

namespace HealthCareAbroad\TermBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('TermBundle:Default:index.html.twig', array('name' => $name));
    }
}
