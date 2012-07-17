<?php

namespace HealthCareAbroad\ProviderBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\ProviderBundle\Entity\Provider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProviderController extends Controller
{
	public function createAction(Request $request)
    {
    	$provider = new Provider();
        $form = $this->createFormBuilder($provider)
            ->add('name', 'text')
            ->getForm();
            
    	$request = $this->getRequest();
    		
    	if ($request->getMethod() == 'POST'){
			$form->bindRequest($request);
			
			if ($form->isValid()){
				$data = $request->request->all();
				var_dump($data);exit;
				$name = $data['form']['name'];
				$email = $data['form']['email'];
				
			}
		}
		return $this->render('ProviderBundle:Provider:create.html.twig', array(
            'form' => $form->createView(),
        ));
	}
	
}
?>