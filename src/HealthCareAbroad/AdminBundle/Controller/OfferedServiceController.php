<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Controller;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\AdminBundle\Entity\OfferedService;
use HealthCareAbroad\HelperBundle\Form\OfferedServiceFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class OfferedServiceController extends Controller
{
    /**
     * View All Offered Service
     * @PreAuthorize("hasAnyRole('ROLE_ADMIN')")
     */
    public function indexAction()
    {
    	$offeredServiceRepository = $this->getDoctrine()->getRepository('AdminBundle:OfferedService');
    	
    	$offeredService = $offeredServiceRepository->getLatestOfferedService();
    	
    	return $this->render('AdminBundle:OfferedService:index.html.twig', array(
                'offeredService' => $offeredService
        ));
    }

    /**
     * Add Offered Service
     * @PreAuthorize("hasAnyRole('ROLE_ADMIN')")
     */
    public function addAction()
    {
        $form = $this->createForm(new OfferedServiceFormType(), new OfferedService());

        return $this->render('AdminBundle:OfferedService:form.html.twig', array(
                'id' => null,
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_offered_service_create')
        ));
    }
    
    /**
     * Edit Offered Service
     * @PreAuthorize("hasAnyRole('ROLE_ADMIN')")
     */
    public function editAction($id)
    {
    	$offeredService = $this->getDoctrine()->getEntityManager()->getRepository('AdminBundle:OfferedService')->find($id);
    	 
    	$form = $this->createForm(new OfferedServiceFormType(), $offeredService);
    
    	return $this->render('AdminBundle:OfferedService:form.html.twig', array(
    					'id' => $id,
    					'form' => $form->createView(),
    					'formAction' => $this->generateUrl('admin_offered_service_update', array('id' => $id))
    	));
    }
    
    /**
     * Save added Offered Service
     * @PreAuthorize("hasAnyRole('ROLE_ADMIN')")
     */
    public function saveAction()
    {
    	$request = $this->getRequest();
    
    	if('POST' != $request->getMethod()) {
    		return new Response("Save requires POST method!", 405);
    	}
    	
    	$id = $request->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();
    	$offeredService = $id ? $em->getRepository('AdminBundle:OfferedService')->find($id) : new OfferedService();
    	$form = $this->createForm(new OfferedServiceFormType(), $offeredService);
    	$form->bind($request);
    
    	if ($form->isValid()) {
    
    		$em->persist($offeredService);
    		$em->flush($offeredService);
    
    		// dispatch event
    		$eventName = $id ? AdminBundleEvents::ON_EDIT_OFFERED_SERVICE : AdminBundleEvents::ON_ADD_OFFERED_SERVICE;
    		$this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $offeredService));
    
    		$request->getSession()->setFlash('success', 'Service has been saved!');
    
    		return $this->redirect($this->generateUrl('admin_offered_service_index'));
    	}
    
    	$formAction = $id ? $this->generateUrl('admin_offered_service_update', array('id' => $id)) : $this->generateUrl('admin_offered_service_create');
    
    	return $this->render('AdminBundle:OfferedService:form.html.twig', array(
    					'id' => $id,
    					'form' => $form->createView(),
    					'formAction' => $formAction
    	));
    }
    
    /**
     * Delete Offered Service / Update status into INACTIVE
     *
     */
    public function updateStatusAction($id)
    {
    
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$offeredService = $em->getRepository('AdminBundle:OfferedService')->find($id);
    
    	if ($offeredService) {
    		$offeredService->setStatus($offeredService->getStatus() ? 0 : 1);
    
    		$em->persist($offeredService);
    		$em->flush($offeredService);
    
    		// dispatch event
    		$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_OFFERED_SERVICE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_OFFERED_SERVICE, $offeredService));
    
    		$result = true;
    	}
    
    	$response = new Response(json_encode($result));
    	$response->headers->set('Content-Type', 'application/json');
    
    	return $response;
    }
    
    
}