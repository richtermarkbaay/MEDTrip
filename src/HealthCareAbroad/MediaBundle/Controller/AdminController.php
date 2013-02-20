<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use HealthCareAbroad\MediaBundle\MediaContext;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class AdminController extends Controller
{
    /**
     * This is convenient but can be tricky because the requirement that the
     * institutionId be defined becomes non-obvious.
     */
    function preExecute()
    {
        $request = $this->getRequest();
        // Check Institution
        if ($request->get('institutionId')) {
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));

            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid Institution');
            }
        }
    }

    public function galleryAction(Request $request)
    {
        $adapter = new ArrayAdapter($this->get('services.media')->retrieveAllMedia($this->institution->getId())->toArray());
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 12));

        return $this->render('MediaBundle:Admin:gallery.html.twig', array(
                'institution' => $this->institution,
                'institutionMedia' => $pager,
                'routes' => DefaultController::getRoutes($request->getPathInfo()),
                'context' => MediaContext::ADMIN_INSTITUTION_GALLERY
        ));
    }

    public function addAction(Request $request)
    {
        return $this->render('MediaBundle:Admin:addMedia.html.twig', array(
                'institution' => $this->institution,
                'multiUpload' => $request->get('multiUpload'),
                'routes' => DefaultController::getRoutes($request->getPathInfo())
        ));
    }

    public function ajaxLoadMedicalCenterMediaAction(Request $request)
    {
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

        $html = $this->render('MediaBundle:Partials:mediaMedicalCenter.html.twig', array(
                'institutionMedicalCenter' => $institutionMedicalCenter,
                'institutionId'	=> $institutionMedicalCenter->getInstitution()->getId()
        ))->getContent();

        $response = new Response(json_encode(array(
                'count' => \count($institutionMedicalCenter->getMedia()),
                'html' => $html
        )));

        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function ajaxAttachMedicalCenterMediaAction(Request $request)
    {
        $success = $this->get('services.media')->addMedicalCenterMedia($request->get('imcId'), $request->get('mediaId'));

        $response = new Response(json_encode($success));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    /**
     * This is a global/generic ADMIN delete media function. 
     * Please use this function instead of creating another.
     * 
     * @author Adelbert D. Silla
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxDeleteAction(Request $request)
    {
        $result = false;

        $mediaId = $request->get('media_id');
        $parentId = $request->request->get('parent_id');
        $parentClass = $request->request->get('parent_class');

        $media = $this->getDoctrine()->getRepository('MediaBundle:Media')->find($mediaId);
        $parentObject = $this->getDoctrine()->getRepository($parentClass)->find($parentId);

        $this->get('services.media')->delete($media, $parentObject);
        
        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    private function extractContext(Request $request)
    {
        $context = array();

        if ($request->get('imcId')) {
            $context = array(
                    'context' => 'institutionMedicalCenter',
                    'contextId' => $request->get('imcId')
            );
        }

        return $context;
    }
}
