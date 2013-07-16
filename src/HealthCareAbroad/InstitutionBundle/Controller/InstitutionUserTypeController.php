<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserTypeEvent;

use HealthCareAbroad\UserBundle\Form\InstitutionUserTypeFormType;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionUserTypeController extends InstitutionAwareController 
{
    /**
     * View all user types
     * 
     */
    public function indexAction()
    {
    	$userTypes = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->getAllEditable($this->institution->getId());
        return $this->render('InstitutionBundle:InstitutionUserType:index.html.twig', array(
            'userTypes' => $userTypes
        ));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STAFF')")
     *
     */
    public function addAction()
    {
    	$userType = new InstitutionUserType();
    	$form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
    	
    	return $this->render('InstitutionBundle:InstitutionUserType:add.html.twig', array(
    			'form' => $form->createView(),
    			'userType' => $userType,
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STAFF')")
     *
     */
    public function editAction()
    {
    	$userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($this->getRequest()->get('id'));
    	if (!$userType) {
    		throw $this->createNotFoundException();
    	}
    	$form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
    	
    	return $this->render('InstitutionBundle:InstitutionUserType:add.html.twig', array(
    			'form' => $form->createView(),
    			'userType' => $userType,
    	));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_STAFF')")
     *
     */
    public function saveAction()
    {
    	$request = $this->getRequest();
    	$institutionId = $this->institution->getId();
    	
    	$id = $request->get('id', 0);
        $userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($id);
        
        if ($id && !$userType) {
            throw $this->createNotFoundException();
        }
        elseif (!$id) {
            $userType = new InstitutionUserType();
            $userType->setStatus(InstitutionUserType::STATUS_ACTIVE);
        }
        
        //assign institution to userType
        $userType->setInstitution($this->institution);

        $form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
        $form->bind($request);
        
        if ($form->isValid()) {
        	
        	//persist data,create institution usertypes
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($userType);
            $em->flush();
            
            // create event on edit and create userTypes and dispatch
            $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_TYPE, $this->get('evens.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_TYPE, $userType));
            
            $request->getSession()->setFlash("success", "{$userType->getName()} user type saved.");
            return $this->redirect($this->generateUrl('institution_userType_index'));
        }
        else {
            return $this->render('InstitutionBundle:InstitutionUserType:add.html.twig', array(
                'form' => $form->createView(),
                'userType' => $userType,
            )); 
        }
    	
    	
    }

}