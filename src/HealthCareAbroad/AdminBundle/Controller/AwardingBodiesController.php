<?php
/**
 * 
 * @author Chaztine Blance
 * Adding of Awarding Bodies Controller
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\AwardingBodies;
use HealthCareAbroad\HelperBundle\Form\AwardingBodiesFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AwardingBodiesController extends Controller
{
	public function indexAction(){
		
		return $this->render('AdminBundle:AwardingBodies:index.html.twig', array(
						'awards' => $this->filteredResult,
						'pager' => $this->pager
		));
	}
	
	public function addAction()
	{
		$form = $this->createForm(New AwardingBodiesFormType(), new AwardingBodies());
	
		return $this->render('AdminBundle:AwardingBodies:form.html.twig', array(
						'id' => null,
						'form' => $form->createView(),
						'formAction' => $this->generateUrl('admin_awardingBodies_create')
		));
	}
	
	public function editAction($id)
	{
		$awardingBodies = $this->getDoctrine()->getEntityManager()
		->getRepository('HelperBundle:AwardingBodies')->find($id);
	
		$form = $this->createForm(New AwardingBodiesFormType(), $awardingBodies);
	
		return $this->render('AdminBundle:AwardingBodies:form.html.twig', array(
						'id' => $id,
						'form' => $form->createView(),
						'formAction' => $this->generateUrl('admin_awardingBodies_update', array('id' => $id))
		));
	}
	
	public function saveAction()
	{
		$request = $this->getRequest();
		
		if('POST' != $request->getMethod()) {
			return new Response("Save requires POST method!", 405);
		}
	
		$id = $request->get('id', null);
		$em = $this->getDoctrine()->getEntityManager();
	
		$awardingBodies = $id ? $em->getRepository('HelperBundle:AwardingBodies')->find($id) : new AwardingBodies();
	
		$form = $this->createForm(New AwardingBodiesFormType(), $awardingBodies);
		$form->bind($request);
	
		if ($form->isValid()) {
			$em->persist($awardingBodies);
			$em->flush($awardingBodies);
	
			// dispatch event
			$eventName = $id ? AdminBundleEvents::ON_EDIT_AWARDING_BODIES : AdminBundleEvents::ON_ADD_AWARDING_BODIES;
			$this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $awardingBodies));
			 
			$request->getSession()->setFlash('success', 'Awarding Bodies has been saved!');
	
			return $this->redirect($this->generateUrl('admin_awardingBodies_index'));
		}
	
		$formAction = $id ? $this->generateUrl('admin_awardingBodies_update', array('id' => $id)) : $this->generateUrl('admin_awardingBodies_create');
	
		return $this->render('AdminBundle:AwardingBodies:form.html.twig', array(
						'id' => $id,
						'form' => $form->createView(),
						'formAction' => $formAction
		));
	}
	
	public function updateStatusAction($id)
	{
		$result = false;
		$em = $this->getDoctrine()->getEntityManager();
		$awardingBodies = $em->getRepository('HelperBundle:AwardingBodies')->find($id);
	
		if ($awardingBodies) {
			$awardingBodies->setStatus($awardingBodies->getStatus() ? $awardingBodies::STATUS_INACTIVE : $awardingBodies::STATUS_ACTIVE);
			$em->persist($awardingBodies);
			$em->flush($awardingBodies);
	
			// dispatch event
			$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_AWARDING_BODIES, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_AWARDING_BODIES, $awardingBodies));
	
			$result = true;
		}
	
		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
}