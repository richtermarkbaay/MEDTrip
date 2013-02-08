<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use HealthCareAbroad\MediaBundle\Services\MediaService;

use HealthCareAbroad\MediaBundle\Entity\Gallery;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Gaufrette\File;

class DefaultController extends Controller
{
    public function addAction(Request $request)
    {
        $imc = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('id'));

        return $this->render('MediaBundle:Default:addImcMedia.html.twig', array(
                        'institution' => $imc->getInstitution(),
                        'imc' => $imc,
                        'multiUpload' => $request->get('multiUpload'),
                        'context' => $request->get('context'),
                        'contextId' => $request->get('contextId'),
                        'routes' => $this->getRoutes($request->getPathInfo())
        ));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMediaAction(Request $request)
    {
        $institution = $request->get('institution');

        return $this->render('MediaBundle:Default:addMedia.html.twig', array(
                        'institution' => $institution,
                        'context' => $request->get('context'),
                        'contextId' => $institution->getId(),
                        'routes' => $this->getRoutes($request->getPathInfo())
        ));
    }

    public function gallerySelectionAction(Request $request)
    {
        $adapter = new ArrayAdapter($this->get('services.media')->retrieveAllMedia($request->get('id'))->toArray());
        $pager = new Pager($adapter, array('page' => $request->get('page'), 'limit' => 12));

        return $this->render('MediaBundle:Default:gallerySelection.html.twig', array(
                        'institution' => $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('id')),
                        'institutionMedia' => $pager,
                        'routes' => $this->getRoutes($request->getPathInfo()),
                        'context' => $request->get('context')
        ));
    }

    public function mediaAttachAction(Request $request)
    {
        $mediaPath = $this->get('services.media')->attachMedia(array(
                        'id' => $request->get('id'),
                        'context' => $request->get('context'),
                        'mediaId' => $request->get('mediaId')
        ));

        if ($mediaPath) {
            $response = array('success' => 1, 'imgSrc' => $mediaPath);
        } else {
            $response = array('success' => 0);
        }

        return new Response(json_encode($response), 200, array('content-type', 'application/json'));
    }

    //upload media to medicalCenter
    public function uploadImcMediaAction(Request $request)
    {
        $response = new Response();
        $imc = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $fileBag = $request->files;
        
        if ($fileBag->get('file')) {
             
            $result = $this->get('services.media')->upload($fileBag->get('file'), $imc);
            if(is_object($result)) {
        
                //$this->institutionMedicalCenter->setLogo($result);
                $imc->addMedia($result);
                $this->get('services.institution')->saveMediaToGallery($imc->getInstitution(), $result);
                $this->get('services.institution_medical_center')->save($imc);
            }
            $response->create('File saved to Medical Center', 200);
        }
        else {
            $response->create('File not detected', 415);
        }
        return $response;
    }
    
    //TODO: refactor
    public function uploadAction(Request $request)
    {
        $response = new Response();

        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));

        $fileBag = $request->files;

        if ($fileBag->has('file')) {
            $isLogo = $request->get('isLogo', false);
            $multiUpload = $request->get('multiUpload');
            
            $result = $this->get('services.media')->upload($fileBag->get('file'), $institution);

            if(is_object($result)) {
                $media = $result;
                $isLogo 
                    ? $this->get('services.institution')->saveMediaAsLogo($institution, $media)
                    : $this->get('services.institution')->saveMediaToGallery($institution, $media);
                
            }

            if (isset($multiUpload) && $multiUpload === '0') {
                $this->get('session')->getFlashBag()->add('notice', 'File successfully uploaded!');

                return $this->render('MediaBundle:Admin:addMedia.html.twig', array(
                    'institution' => $institution,
                    'routes' => MediaService::getRoutes($request->getPathInfo()),
                    'multiUpload' => $multiUpload));
            }

            return $response;

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

    /**
     * @param string $pathInfo
     */
    public static function getRoutes($pathInfo)    {
        $routes = array();

        if (strpos($pathInfo, 'admin') === 1) {
            $routes['gallery'] = 'admin_institution_gallery';
            $routes['gallery_add'] = 'admin_institution_gallery_add';
            $routes['gallery_selection'] = 'admin_gallery_selection';
            $routes['media_attach'] = 'admin_media_attach';
            $routes['media_upload'] = 'admin_media_upload';
            $routes['media_edit_caption'] = 'admin_media_edit_caption';
            $routes['media_delete'] = 'admin_media_delete';
        } else if (strpos($pathInfo, 'institution') === 1) {
            $routes['gallery'] = 'institution_gallery';
            $routes['gallery_add'] = 'institution_gallery_add';
            $routes['gallery_selection'] = 'institution_gallery_selection';
            $routes['media_attach'] = 'institution_media_attach';
            $routes['media_upload'] = 'institution_media_upload';
            $routes['media_edit_caption'] = 'institution_media_edit_caption';
            $routes['media_delete'] = 'institution_media_delete';
        } else {
            throw new \Exception('Invalid pathinfo: '. $pathInfo);
        }

        return $routes;
    }
}
