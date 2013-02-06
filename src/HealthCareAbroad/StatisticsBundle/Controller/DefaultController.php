<?php

namespace HealthcareAbroad\StatisticsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('StatisticsBundle:Default:index.html.twig', array('name' => $name));
    }
}
