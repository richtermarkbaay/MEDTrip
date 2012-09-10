<?php
namespace HealthCareAbroad\LogBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function showVersionsAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw $this->createNotFoundException("Only supports AJAX request");
        }
        $objectId = $request->get('objectId', null);
        $objectClass = $request->get('objectClass', null);
        
        if ($objectId === null || $objectClass === null) {
            return new Response("objectId and objectClass are required parameters", 400);
        }
        
        $object = $this->getDoctrine()->getRepository($objectClass)->find($objectId);
        exit;
    }
}