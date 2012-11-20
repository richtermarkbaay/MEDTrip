<?php
/**
 * 
 * @author Chaztine Blance
 * Adding of Awarding Body Controller
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\AwardingBody;
use HealthCareAbroad\HelperBundle\Form\AwardingBodyFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AwardingBodyController extends Controller
{
	public function indexAction(){
		
		return $this->render('AdminBundle:AwardingBody:index.html.twig', array(
						'awards' => $this->filteredResult,
						'pager' => $this->pager
		));
	}
	
	public function addAction()
	{
		$form = $this->createForm(New AwardingBodyFormType(), new AwardingBody());
	
		return $this->render('AdminBundle:AwardingBody:form.html.twig', array(
						'id' => null,
						'form' => $form->createView(),
						'formAction' => $this->generateUrl('admin_awardingBody_create')
		));
	}
	
	public function editAction($id)
	{
		$awardingBody = $this->getDoctrine()->getEntityManager()
		->getRepository('HelperBundle:AwardingBody')->find($id);
	
		$form = $this->createForm(New AwardingBodyFormType(), $awardingBody);
	
		return $this->render('AdminBundle:AwardingBody:form.html.twig', array(
						'id' => $id,
						'form' => $form->createView(),
						'formAction' => $this->generateUrl('admin_awardingBody_update', array('id' => $id))
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
	
		$awardingBody = $id ? $em->getRepository('HelperBundle:AwardingBody')->find($id) : new AwardingBody();
	
		$form = $this->createForm(New AwardingBodyFormType(), $awardingBody);
		$form->bind($request);
	
		if ($form->isValid()) {
			$em->persist($awardingBody);
			$em->flush($awardingBody);
	
			// dispatch event
			$eventName = $id ? AdminBundleEvents::ON_EDIT_AWARDING_BODY : AdminBundleEvents::ON_ADD_AWARDING_BODY;
			$this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $awardingBody));
			 
			$request->getSession()->setFlash('success', 'Awarding Body has been saved!');
	
			return $this->redirect($this->generateUrl('admin_awardingBody_index'));
		}
	
		$formAction = $id ? $this->generateUrl('admin_awardingBody_update', array('id' => $id)) : $this->generateUrl('admin_awardingBody_create');
	
		return $this->render('AdminBundle:AwardingBody:form.html.twig', array(
						'id' => $id,
						'form' => $form->createView(),
						'formAction' => $formAction
		));
	}
	
	public function updateStatusAction($id)
	{
		$result = false;
		$em = $this->getDoctrine()->getEntityManager();
		$awardingBody = $em->getRepository('HelperBundle:AwardingBody')->find($id);
	
		if ($awardingBody) {
			$awardingBody->setStatus($awardingBody->getStatus() ? $awardingBody::STATUS_INACTIVE : $awardingBody::STATUS_ACTIVE);
			$em->persist($awardingBody);
			$em->flush($awardingBody);
	
			// dispatch event
			$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_AWARDING_BODY, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_AWARDING_BODY, $awardingBody));
	
			$result = true;
		}
	
		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
	
		return $response;
	}
}