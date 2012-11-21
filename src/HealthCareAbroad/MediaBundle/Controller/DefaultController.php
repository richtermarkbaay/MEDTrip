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
    public function gallerySelectionAction(Request $request)
    {
        $adapter = new ArrayAdapter($this->get('services.media')->retrieveAllMedia($request->get('id'))->toArray());
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 1));

        $html = $this->render('MediaBundle:Default:gallerySelection.html.twig', array(
            'institution' => $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('id')),
            'institutionMedia' => $pager
        ))->getContent();

        return new Response($html, 200);
    }

    //TODO: refactor
    public function uploadAction(Request $request)
    {
        $response = new Response();

        $institutionId = $request->get('institutionId');
        $fileBag = $request->files;

        if ($fileBag->has('file')) {
            $errorCode = $this->get('services.media')->upload($fileBag->get('file'), $institutionId, $this->extractContext($request));

            $multiUpload = $request->get('multiUpload');

            if (isset($multiUpload) && $multiUpload === '0') {

                $this->get('session')->getFlashBag()->add('notice', 'File successfully uploaded!');

                return $this->render('MediaBundle:Admin:addMedia.html.twig', array(
                        'institution' => $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId),
                        'multiUpload' => $multiUpload));
            }

            return $response->create('Error code: '.$errorCode);

        } else {

            return $response->create('File not detected', 415);
        }
    }

    public function deleteAction(Request $request)
    {
        $success = $this->get('services.media')->delete($request->get('id'), $request->get('institutionId'));

        return new Response($success);
    }

    public function editCaptionAction(Request $request)
    {
        $media = $this->get('services.media')->editMediaCaption($request->get('id'), $request->get('institutionId'), $request->get('caption'));

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
