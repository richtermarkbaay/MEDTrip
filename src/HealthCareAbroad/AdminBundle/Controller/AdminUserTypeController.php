<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Events\AdminUserTypeEvents;

use HealthCareAbroad\AdminBundle\Events\CreateAdminUserTypeEvent;

use HealthCareAbroad\UserBundle\Form\AdminUserTypeFormType;

use HealthCareAbroad\UserBundle\Entity\AdminUserType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AdminUserTypeController extends Controller 
{
    /**
     * View all user types
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function indexAction()
    {
        $userTypes = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->getAllEditable();
        return $this->render('AdminBundle:AdminUserType:index.html.twig', array(
            'userTypes' => $userTypes
        ));
    }
    
    /**
     * Create new user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function addAction()
    {
        $userType = new AdminUserType();
        $form = $this->createForm(new AdminUserTypeFormType(), $userType);
        
        return $this->render('AdminBundle:AdminUserType:form.html.twig', array(
            'form' => $form->createView(),
            'userType' => $userType,
        ));
    }
    
    /**
     * Edit a user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function editAction()
    {
        $id = $this->getRequest()->get('id', 0);
        $userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find($id);
        
        if (!$userType) {
            throw $this->createNotFoundException();
        }
        
        $form = $this->createForm(new AdminUserTypeFormType(), $userType);
        
        return $this->render('AdminBundle:AdminUserType:form.html.twig', array(
            'form' => $form->createView(),
            'userType' => $userType,
        ));
    }
    
    /**
     * Do save changes to user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        
        $id = $request->get('id', 0);
        $userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find($id);
        
        if ($id && !$userType) {
            throw $this->createNotFoundException();
        }
        elseif (!$id) {
            $userType = new AdminUserType();
            $userType->setStatus(AdminUserType::STATUS_ACTIVE);
        }
                
        $form = $this->createForm(new AdminUserTypeFormType(), $userType);
        $form->bind($request);
        if ($form->isValid()) {
            //$userType = $form->getData();
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($userType);
            $em->flush();
            
            //// create event on addRoleToUserType and dispatch
            $event = new CreateAdminUserTypeEvent($userType);
            $this->get('event_dispatcher')->dispatch(AdminUserTypeEvents::ON_ADD_ADMIN_USER_TYPE, $event);
            
            $request->getSession()->setFlash("success", "{$userType->getName()} user type saved.");
            return $this->redirect($this->generateUrl('admin_userType_index'));
        }
        else {
            return $this->render('AdminBundle:AdminUserType:form.html.twig', array(
                'form' => $form->createView(),
                'userType' => $userType,
            )); 
        }
    }
}