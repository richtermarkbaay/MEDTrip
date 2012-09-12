<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;

use Gaufrette\File;

class DefaultController extends Controller
{
    public function galleryAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');				
        
        return $this->render('MediaBundle:Institution:gallery.html.twig', array(
                'institutionId' => $institutionId,
                'institutionMedia' => $this->get('services.media')->retrieveAllMedia($institutionId)
        ));
    }
    
    public function addAction(Request $request)
    {
        $institutionId = $request->getSession()->get('institutionId');				
        
        return $this->render('MediaBundle:Institution:addMedia.html.twig', array(
                'institutionId' => $institutionId
        ));
    }
    
    public function uploadAction(Request $request)
    {
        $response = new Response(); 
        $institutionId = $request->getSession()->get('institutionId');

        $fileBag = $request->files;
        
        if ($fileBag->has('file')) {
            $errorCode = 0;
            //commented out for testing
            //$errorCode = $this->get('services.media')->upload($fileBag->get('file'), $institutionId, $this->extractContext($request));
        } else {
            return $response->create('File not detected', 415); 
        }
        
        return $response->create('Error code: '.$errorCode);
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
