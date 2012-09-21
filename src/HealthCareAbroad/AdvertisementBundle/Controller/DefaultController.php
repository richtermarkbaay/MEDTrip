<?php

namespace HealthCareAbroad\AdvertisementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AdvertisementBundle:Default:index.html.twig', array('name' => $name));
    }
}
