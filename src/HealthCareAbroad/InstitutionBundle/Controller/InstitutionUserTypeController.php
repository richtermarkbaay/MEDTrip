<?php 
namespace HealthCareAbroad\InstitutionBundle\Controller;

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
    
    public function addAction()
    {
    	$userType = new InstitutionUserType();
    	$form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
    	
    	return $this->render('InstitutionBundle:InstitutionUserType:add.html.twig', array(
    			'form' => $form->createView(),
    			'userType' => $userType,
    	));
    }
    
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
     * Create new user type
     *
     * @PreAuthorize("hasAnyRole('LISTING_CREATOR')")
     */
    public function saveAction()
    {
   		$request = $this->getRequest();
        
        $id = $request->get('id');
        $userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($id);
        
        if ($id && !$userType) {
            throw $this->createNotFoundException();
        }
        elseif (!$id) {
            $userType = new InstitutionUserType();
            $userType->setStatus(InstitutionUserType::STATUS_ACTIVE);
        }
        
        $form = $this->createForm(new InstitutionUserTypeFormType(), $userType);
        $form->bind($request);
        
        if ($form->isValid()) {
        	
        	//persist data,create institution usertypes
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($userType);
            $em->flush();
            
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