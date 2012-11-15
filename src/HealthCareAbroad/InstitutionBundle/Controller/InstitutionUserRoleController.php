<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

class InstitutionUserRoleController extends InstitutionAwareController
{
    /**
     * View all user roles
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function indexAction()
    {
        //get userRoles
        $userRoles = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole')->getAssignablePermissions();
        return $this->render('InstitutionBundle:InstitutionUserRole:index.html.twig', array(
            'userRoles' => $userRoles
        ));
    }

    /**
     * View user roles of a user type
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function viewByUserTypeAction()
    {
        $userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($this->getRequest()->get('id', 0));

        if (!$userType) {
            throw $this->createNotFoundException();
        }

        $userRoleRepo = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole');
        $assignableUserRoles = $userRoleRepo->getAssignablePermissionsByUserType($userType);
        $currentRoles = $userType->getInstitutionUserRoles();
        return $this->render('InstitutionBundle:InstitutionUserRole:viewByUserType.html.twig', array(
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
        $userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($this->getRequest()->get('userTypeId', 0));
        $userRole = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole')->find($this->getRequest()->get('userRoleId', 0));

        if (!$userType || !$userRole) {
            throw $this->createNotFoundException();
        }
        $userType->addInstitutionUserRole($userRole);

        $em = $this->getDoctrine()->getEntityManager();

        try {
            $em->persist($userType);
            $em->flush();

            // dispatch event
            $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_TYPE_ROLE, $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION_USER_TYPE_ROLE, $userType));
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
        $userType = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserType')->find($this->getRequest()->get('userTypeId', 0));
        $userRole = $this->getDoctrine()->getRepository('UserBundle:InstitutionUserRole')->find($this->getRequest()->get('userRoleId', 0));

        if (!$userType || !$userRole) {
            throw $this->createNotFoundException();
        }
        $userType->removeInstitutionUserRole($userRole);
        $em = $this->getDoctrine()->getEntityManager();

        $em->persist($userType);
        $em->flush();

        // dispatch event
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_DELETE_INSTITUTION_USER_TYPE_ROLE, $this->get('events.factory')->create(InstitutionBundleEvents::ON_DELETE_INSTITUTION_USER_TYPE_ROLE, $userType));

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

?>