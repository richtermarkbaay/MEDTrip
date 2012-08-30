<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\Country;
use HealthCareAbroad\HelperBundle\Form\CountryFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CountryController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_COUNTRIES')")
     */
    public function indexAction()
    {
    	return $this->render('AdminBundle:Country:index.html.twig', array('countries' => $this->filteredResult));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
	public function addAction()
	{
    	$form = $this->createForm(New CountryFormType(), new Country());

    	return $this->render('AdminBundle:Country:form.html.twig', array(
			'id' => null,
			'form' => $form->createView(),  
			'formAction' => $this->generateUrl('admin_country_create')
		));
	}
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function editAction($id)
    {
    	$country = $this->getDoctrine()->getEntityManager()
    			->getRepository('HelperBundle:Country')->find($id);
    	
    	$form = $this->createForm(New CountryFormType(), $country);

    	return $this->render('AdminBundle:Country:form.html.twig', array(
    			'id' => $id,
    			'form' => $form->createView(), 
    			'formAction' => $this->generateUrl('admin_country_update', array('id' => $id))
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_COUNTRY')")
     */
    public function saveAction()
    {
    	$request = $this->getRequest();
    	if('POST' != $request->getMethod()) {
    		return new Response("Save requires POST method!", 405);
    	}

    	$id = $request->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();

		$country = $id ? $em->getRepository('HelperBundle:Country')->find($id) : new Country();

		$form = $this->createForm(New CountryFormType(), $country);
   		$form->bind($request);

   		if ($form->isValid()) {
   			$em->persist($country);
   			$em->flush($country);

   			$request->getSession()->setFlash('success', 'Country has been saved!');

   			return $this->redirect($this->generateUrl('admin_country_index'));
		}

		$formAction = $id 
			? $this->generateUrl('admin_country_update', array('id' => $id))
			: $this->generateUrl('admin_country_create');
		
		return $this->render('AdminBundle:Country:form.html.twig', array(
				'id' => $id,
				'form' => $form->createView(),
				'formAction' => $formAction 
		));				
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_COUNTRY')")
     */
    public function updateStatusAction($id)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$country = $em->getRepository('HelperBundle:Country')->find($id);

		if ($country) {
			$country->setStatus($country->getStatus() ? $country::STATUS_INACTIVE : $country::STATUS_ACTIVE);
			$em->persist($country);
			$em->flush($country);
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
}