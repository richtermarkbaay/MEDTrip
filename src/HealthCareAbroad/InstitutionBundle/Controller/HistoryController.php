<?php
/**
 * Show edit history of an object
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

class HistoryController extends Controller
{
    /**
     * Show edit history of an object
     * Required REQUEST parameters are:
     *     objectId - int
     *     objectClass - base64_encoded fully qualified class name
     * 
     * @param Request $request
     * @return \HealthCareAbroad\AdminBundle\Controller\Response
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
        
        $template = 'InstitutionBundle:History:editHistory.html.twig';
        if ($request->isXmlHttpRequest()) {
            $template = 'InstitutionBundle:History:versionList.html.twig';
        }
        
        $objectName = $object->__toString();
        return $this->render($template, array(
            'versions' => $versionEntries,
            'objectName' => $objectName
        ));
    }
}
