<?php

namespace HealthCareAbroad\DoctorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DoctorBundle:Default:index.html.twig', array('name' => $name));
    }
}
