<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Events\TagEvents;

use HealthCareAbroad\AdminBundle\Events\CreateTagEvent;

use HealthCareAbroad\HelperBundle\Form\TagType;

use HealthCareAbroad\HelperBundle\Form\TagTypeListType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\Tag;

class TagController extends Controller
{
    public function indexAction()
    {
		$tags = $this->getDoctrine()->getEntityManager()->getRepository('HelperBundle:Tag')->findAll();
    	$data = array('tags'=>$tags, 'types' => Tag::$TYPES);
    	return $this->render('AdminBundle:Tag:index.html.twig', $data);
    }

    public function addAction()
    {
    	$form = $this->createForm(new TagType(), new Tag());
    	$params = array('form' => $form->createView());
    	return $this->render('AdminBundle:Tag:create.html.twig', $params);
    }
    
    public function editAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$tag = $em->getRepository('HelperBundle:Tag')->find($id);
    	$tagType = new TagType($tag->getId()); 
    	$form = $this->createForm($tagType, $tag);

    	$params = array('form' => $form->createView());
    	return $this->render('AdminBundle:Tag:create.html.twig', $params);
    }

    public function saveAction()
    {
    	$request = $this->getRequest();

    	if ('POST' == $request->getMethod()) {    		
    		$em = $this->getDoctrine()->getEntityManager();

			$tag = $request->get('id')
				? $em->getRepository('HelperBundle:Tag')->find($request->get('id')) 
				: new Tag();

			$form = $this->createForm(new TagType(), $tag);
    		$form->bind($request);

    		if ($form->isValid()) {
    			$tag->setStatus(Tag::STATUS_ACTIVE);
    			$em->persist($tag);
    			$em->flush($tag);

    			if($request->get('id')){
    				//// create event on addTAg and dispatch
    				$event = new CreateTagEvent($tag);
    				$this->get('event_dispatcher')->dispatch(TagEvents::ON_ADD_TAG, $event);
    			}
    			else {
    				//// create event on addTAg and dispatch
    				$event = new CreateTagEvent($tag);
    				$this->get('event_dispatcher')->dispatch(TagEvents::ON_EDIT_TAG, $event);
    			}
    			
    			
    			$msg = $request->get('id') 
    				? '"' .$tag->getName() . '" tag has been updated!' 
    				: 'New Tag has been added!'; 
    			$request->getSession()->setFlash('success', $msg);
    			return $this->redirect($this->generateUrl('admin_tagHomepage'));
			} else {
				return $this->redirect($this->generateUrl('admin_tagAdd'));
			}
    	}
    }
    
    public function updateStatusAction($id)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
		$tag = $em->getRepository('HelperBundle:Tag')->find($id);

		if($tag) {
			$status = $tag->getStatus() == Tag::STATUS_ACTIVE ? Tag::STATUS_INACTIVE : Tag::STATUS_ACTIVE;
			$tag->setStatus($status);
			$em->persist($tag);
			$em->flush($tag);
			
			//// create event on addTAg and dispatch
			$event = new CreateTagEvent($tag);
			$this->get('event_dispatcher')->dispatch(TagEvents::ON_EDIT_TAG, $event);
			
			$result = true;
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');
		
		return $response;
    }
}
