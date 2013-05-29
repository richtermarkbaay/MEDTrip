<?php

namespace HealthCareAbroad\DoctorBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DoctorBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function searchDoctorsAction()
    {
        $criteria = $this->getRequest()->get('criteria');

        $jsonDoctors = $this->get('services.doctor')->searchDoctors($criteria, 'json');

        return new Response($jsonDoctors, 200, array('Content-Type'=>'application/json'));
    }
}
