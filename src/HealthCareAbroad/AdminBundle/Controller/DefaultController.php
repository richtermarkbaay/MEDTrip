<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DefaultController extends Controller
{
    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }

    /**
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function manageHcaDataAction()
    {
    	return $this->render('AdminBundle:Default:manageHcaDataDashboard.html.twig');
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN')")
     */
    public function settingsAction()
    {
        return $this->render('AdminBundle:Default:settings.html.twig');
    }
    
    public function error403Action()
    {
        return $this->render('AdminBundle:Exception:error403.html.twig');
    }
    
    /**
     * Show edit history of an object
     * Required REQUEST parameters are:
     *     objectId - int
     *     objectClass - base64_encoded fully qualified class name
     * 
     * @param Request $request
     * @return \HealthCareAbroad\AdminBundle\Controller\Response
     * @author Allejo Chris G. Velarde
     */
    public function showEditHistoryAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException("Only supports AJAX request");
        }
        $objectId = $request->get('objectId', null);
        $objectClass = $request->get('objectClass', null);
        
        if ($objectId === null || $objectClass === null) {
            return new Response("objectId and objectClass are required parameters", 400);
        }
        
        $objectClass = \base64_decode($objectClass);
        if (!\class_exists($objectClass)) {
            throw $this->createNotFoundException("Cannot view history of invalid class {$objectClass}");
        }
        
        $object = $this->getDoctrine()->getRepository($objectClass)->find($objectId);
        if (!$object) {
            throw $this->createNotFoundException("Object #{$objectId} of class {$objectClass} does not exist.");
        }
        
        $service = $this->get('services.log.entity_version');
        $versionEntries = $service->getObjectVersionEntries($object);
        
        $template = 'AdminBundle:Default:editHistory.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'AdminBundle:Default:versionsList.html.twig';
        }
        
        $objectName = $object->__toString();
        
        return $this->render($template, array(
            'versions' => $versionEntries,
            'objectName' => $objectName
        ));
    }
}
