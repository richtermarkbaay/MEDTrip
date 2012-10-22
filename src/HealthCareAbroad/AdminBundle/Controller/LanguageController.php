<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Controller;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\AdminBundle\Entity\Language;
use HealthCareAbroad\HelperBundle\Form\LanguageFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class LanguageController extends Controller
{
    /**
     * View All languages
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_LANGUAGE')")
     */
    public function indexAction()
    {
    	
    	$language = $this->getDoctrine()->getRepository('AdminBundle:Language')->getActiveLanguages();
    	
    	return $this->render('AdminBundle:Language:index.html.twig', array(
                'Language' => $language
        ));
    }

    /**
     * Add language
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_LANGUAGE')")
     */
    public function addAction()
    {
        $form = $this->createForm(new LanguageFormType(), new Language());

        return $this->render('AdminBundle:Language:form.html.twig', array(
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_language_create'),
        		'newObject' => true
        ));
    }
    
    /**
     * Edit language
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_LANGUAGE')")
     */
    public function editAction($id)
    {
    	$language = $this->getDoctrine()->getEntityManager()->getRepository('AdminBundle:Language')->find($id);
    
    	$form = $this->createForm(new LanguageFormType(), $language);
    
    	return $this->render('AdminBundle:Language:form.html.twig', array(
    					'id' => $id,
    					'form' => $form->createView(),
    					'formAction' => $this->generateUrl('admin_language_update', array('id' => $id)),
    					'newObject' => false
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_LANGUAGE')")
     */
    public function saveAction()
    {
    	$request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }
        
    	$id = $request->get('id', null);
    	$em = $this->getDoctrine()->getEntityManager();
    	
    	$language = $id ? $em->getRepository('AdminBundle:Language')->find($id) : new Language();
    	
    	$form = $this->createForm(new LanguageFormType(), $language);
    		$form->bind($request);
    
    	if ($form->isValid()) {
    
    		$em->persist($language);
    		$em->flush($language);
    
    		// dispatch event
    		$eventName = $id ? AdminBundleEvents::ON_EDIT_LANGUAGE : AdminBundleEvents::ON_ADD_LANGUAGE;
    		$this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $language));
    
    		$request->getSession()->setFlash('success', 'Service has been saved!');
    
    		return $this->redirect($this->generateUrl('admin_language_index'));
    	}
    
    	$formAction = $id ? $this->generateUrl('admin_language_update', array('id' => $id)) : $this->generateUrl('admin_language_create');
    
    	return $this->render('AdminBundle:Language:form.html.twig', array(
    					'id' => $id,
    					'form' => $form->createView(),
    					'formAction' => $formAction
    	));
    }
    
    /**
     * Delete Language / Update status into INACTIVE
     *
     */
    public function updateStatusAction($id)
    {
    
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$language = $em->getRepository('AdminBundle:Language')->find($id);
    
    	if ($language) {
    		
    		$language->setStatus($language->getStatus() ? $language::STATUS_INACTIVE : $language::STATUS_ACTIVE);
    	
    		$em->persist($language);
    		$em->flush($language);
    
    		// dispatch event
    		$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_LANGUAGE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_LANGUAGE, $language));
    
    		$result = true;
    	}
    
    	$response = new Response(json_encode($result));
    	$response->headers->set('Content-Type', 'application/json');
    
    	return $response;
    }
    
    
    
    
}