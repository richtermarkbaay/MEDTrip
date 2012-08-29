<?php
/**
 * @author adelbertsilla
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Form\CityFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class CityController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_CENTERS')")
     * 
     */
    public function indexAction()
    {
		return $this->render('AdminBundle:City:index.html.twig', array('cities' => $this->filteredResult));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
	public function addAction()
	{
    	$form = $this->createForm(New CityFormType(), new City());

    	return $this->render('AdminBundle:City:form.html.twig', array(
			'id' => null,
			'form' => $form->createView(),  
			'formAction' => $this->generateUrl('admin_city_create')
		));
	}
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function editAction($id)
    {
    	$city = $this->getDoctrine()->getEntityManager()
    			->getRepository('HelperBundle:City')->find($id);
    	
    	$form = $this->createForm(New CityFormType(), $city);

    	return $this->render('AdminBundle:City:form.html.twig', array(
			'id' => $id,
			'form' => $form->createView(), 
			'formAction' => $this->generateUrl('admin_city_update', array('id' => $id))
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function saveAction()
    {
    	$id = $this->getRequest()->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();

		$city = $id ? $em->getRepository('HelperBundle:City')->find($id) : new City();

		$form = $this->createForm(New CityFormType(), $city);
   		$form->bind($this->getRequest());

   		if ($form->isValid()) {
   			$em->persist($city);
   			$em->flush($city);

   			$this->getRequest()->getSession()->setFlash('success', 'City has been saved!');
   			return $this->redirect($this->generateUrl('admin_city_index'));
		}

		$formAction = $id 
			? $this->generateUrl('admin_city_update', array('id' => $id))
			: $this->generateUrl('admin_city_create');
		
		return $this->render('AdminBundle:City:form.html.twig', array(
			'id' => $id,
			'form' => $form->createView(),
			'formAction' => $formAction 
		));				
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_MANAGE_MEDICAL_CENTER')")
     */
    public function updateStatusAction($id)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$city = $em->getRepository('HelperBundle:City')->find($id);

		if ($city) {
			$city->setStatus($city->getStatus() ? 0 : 1);
			$em->persist($city);
			$em->flush($city);
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }
}