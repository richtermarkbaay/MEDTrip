<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use \PDOException;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AdminUserRoleController extends Controller 
{
    /**
     * View all user roles
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function indexAction()
    {
        //$userRoles = $this->getDoctrine()->getRepository('UserBundle:AdminUserRole')->getAssignablePermissions();
        $userRoles = $this->filteredResult;
        return $this->render('AdminBundle:AdminUserRole:index.html.twig', array(
            'userRoles' => $userRoles,
            'pager' => $this->pager
        ));
    }
    
    /**
     * View user roles of a user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function viewByUserTypeAction()
    {
        $userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find($this->getRequest()->get('id', 0));
        
        if (!$userType) {
            throw $this->createNotFoundException();
        }
        
        $userRoleRepo = $this->getDoctrine()->getRepository('UserBundle:AdminUserRole');
        $assignableUserRoles = $userRoleRepo->getAssignablePermissionsByUserType($userType);
        $currentRoles = $userType->getAdminUserRoles();
        
        return $this->render('AdminBundle:AdminUserRole:viewByUserType.html.twig', array(
            'assignableUserRoles' => $assignableUserRoles,
            'userType' => $userType,
            'currentRoles' => $currentRoles
        ));
    }
    
    /**
     * Add a role to a user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function addRoleToUserTypeAction()
    {
        $userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find($this->getRequest()->get('userTypeId', 0));
        $userRole = $this->getDoctrine()->getRepository('UserBundle:AdminUserRole')->find($this->getRequest()->get('userRoleId', 0));
        
        if (!$userType || !$userRole) {
            throw $this->createNotFoundException();
        }
        $userType->addAdminUserRole($userRole);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        try {
            $em->persist($userType);
            $em->flush();
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_ADD_ADMIN_USER_TYPE_ROLE, $this->get('events.factory')->create(AdminBundleEvents::ON_ADD_ADMIN_USER_TYPE_ROLE, $userType));
        }
        catch (\PDOException $e) {
            return $this->_errorResponse(500, $e->getMessage());
        }
        
        return $this->_jsonResponse(array('success' => 1));
    }
    
    /**
     * Remove a role from a user type
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function removeRoleFromUserTypeAction()
    {
        $userType = $this->getDoctrine()->getRepository('UserBundle:AdminUserType')->find($this->getRequest()->get('userTypeId', 0));
        $userRole = $this->getDoctrine()->getRepository('UserBundle:AdminUserRole')->find($this->getRequest()->get('userRoleId', 0));
        
        if (!$userType || !$userRole) {
            throw $this->createNotFoundException();
        }
        $userType->removeAdminUserRole($userRole);
        
        $em = $this->getDoctrine()->getEntityManager();
        
        $em->persist($userType);
        $em->flush();
        
        // dispatch event
        $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_DELETE_ADMIN_USER_TYPE_ROLE, $this->get('events.factory')->create(AdminBundleEvents::ON_DELETE_ADMIN_USER_TYPE_ROLE, $userType));
        
        return $this->_jsonResponse(array('success' => 1));
    }
    
    private function _errorResponse($code=400, $message=null)
    {
        $response = new Response();
        $response->setStatusCode($code);
        if ($message != null){
            $response->setContent($message);
        }
    
        return $response;
    }
    
    private function _jsonResponse($data=array(), $code=200)
    {
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }
}