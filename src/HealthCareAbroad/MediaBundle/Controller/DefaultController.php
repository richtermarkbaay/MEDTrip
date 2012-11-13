<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class DefaultController extends Controller
{
    //TODO: refactor
    public function uploadAction(Request $request)
    {
        $response = new Response();
        $session = $this->get('session');
        $institutionId = $session->get('institutionId');
        $fileBag = $request->files;

        if ($fileBag->has('file')) {
            //$errorCode = $this->get('services.media')->upload($fileBag->get('file'), $institutionId, $this->extractContext($request));

            $multiUpload = $request->get('multiUpload');

            if (isset($multiUpload) && $multiUpload === '0') {

                $session->getFlashBag()->add('notice', 'File successfully uploaded!');

                return $this->render('MediaBundle:Admin:addMedia.html.twig', array(
                        'institutionId' => $institutionId,
                        'multiUpload' => $multiUpload));
            }

            return $response->create('Error code: '.$errorCode);

        } else {

            return $response->create('File not detected', 415);
        }
    }

    public function deleteAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');

        $success = $this->get('services.media')->delete($request->get('id'), $institutionId);

        return new Response($success);
    }

    public function editCaptionAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');

        $media = $this->get('services.media')->editMediaCaption($request->get('id'), $institutionId, $request->get('caption'));

        $response = 0;
        if ($media) {
            $response = $media->getId();
        }

        return new Response($response);
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
