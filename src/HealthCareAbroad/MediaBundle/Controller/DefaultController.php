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
		$session = $this->getRequest()->getSession();
		$institutionId = $session->get('institutionId');				
		
		return $this->render('MediaBundle:Institution:gallery.html.twig', array(
				'institutionId' => $institutionId,
				'institutionMedia' => $this->get('services.media')->retrieveAllMedia($institutionId)
		));
	}
	
	public function addAction(Request $request)
	{
		$institutionId = $this->getRequest()->getSession()->get('institutionId');				
		
		return $this->render('MediaBundle:Institution:addMedia.html.twig', array(
				'institutionId' => $institutionId
		));
	}
	
	public function uploadAction(Request $request)
	{
		$response = new Response(); 

		$institutionId = $this->getRequest()->getSession()->get('institutionId');
		
		$fileBag = $request->files;
		if ($fileBag->has('file')) {
			$errorCode = $this->get('services.media')->upload($fileBag->get('file'), $institutionId);
		} else {
			return $response->create('File not detected', 415); 
		}
		
		return $response->create('Error code: '.$errorCode);
	}
}
