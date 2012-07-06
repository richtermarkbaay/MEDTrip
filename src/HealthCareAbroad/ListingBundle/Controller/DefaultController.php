<?php

namespace HealthCareAbroad\ListingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ListingBundle:Default:index.html.twig', array('name' => $name));
    }
}
