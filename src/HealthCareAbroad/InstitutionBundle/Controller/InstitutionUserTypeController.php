<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionUserTypeEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionUserTypeEvent;

use HealthCareAbroad\UserBundle\Form\InstitutionUserTypeFormType;
use HealthCareAbroad\UserBundle\Entity\InstitutionUserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionUserTypeController extends Controller 
{
    /**
     * View all user types
     * 
     */
    public function indexAction()
    {
    	$institutionId = $this->getRequest()->get('institutionId', null);
        
    	if (!$institutionId){
    		// no account id in parameter, editing currently logged in account
    		$session = $this->getRequest()->getSession();
    		$institutionId = $session->get('institutionId');
    	}
    	$userTypes = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->getAllEditable($institutionId);
    	
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
    	$userTypeId = $this->getRequest()->get('id');
    	
    	$userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($userTypeId);
    	
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
   		//get data of institutionId 
    	$institutionId = $request->get('institutionId', null);
    	if (!$institutionId){
    		// no account id in parameter, editing currently logged in account
    		$session = $request->getSession();
    		$institutionId = $session->get('institutionId');
    	}
		$institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);
        
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
        $userType->setInstitution($institution);

        $form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
        $form->bind($request);
        
        if ($form->isValid()) {
        	
        	//persist data,create institution usertypes
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($userType);
            $em->flush();
            
            //// create event on edit and create userTypes and dispatch
            $event = new CreateInstitutionUserTypeEvent($userType);
            $this->get('event_dispatcher')->dispatch(InstitutionUserTypeEvents::ON_ADD_INSTITUTION_USER_TYPE, $event);
            
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
    
    public function viewUserTypesAction()
    {
    	return $this->render('InstitutionBundle:InstitutionUserType:viewUserType.html.twig'
    	);
    }
}