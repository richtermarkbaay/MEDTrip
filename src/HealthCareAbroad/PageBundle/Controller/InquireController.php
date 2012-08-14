<?php

namespace HealthCareAbroad\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\PageBundle\Form\InquireType;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
class InquireController extends Controller
{
    public function indexAction()
    {
    	$form = $this->createForm(new InquireType());
    
        return $this->render('PageBundle:Inquire:index.html.twig', array(
        		'form' => $form->createView(),
        ));
    }
}
