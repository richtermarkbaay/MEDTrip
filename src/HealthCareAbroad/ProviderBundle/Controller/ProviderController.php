<?php

namespace HealthCareAbroad\ProviderBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ProviderBundle\Entity\Provider;

class ProviderController extends Controller
{
	public function createAction()
	{
		$provider = new Provider();
        $form = $this->createFormBuilder()
            ->add('Provider_Institution_Name', 'text')
            ->add('Email', 'text')
            ->getForm();
            
		return $this->render('ProviderBundle:Provider:create.html.twig', array(
            						'form' => $form->createView()));
	}
}

?>