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
	
	public function indexAction(Request $request) 
	{
		$filesystems = $this->get('chromedia_gaufrette.filesystem_map');

		echo '<br/><br/>filesystem_map';
		var_dump($filesystems);		
		
		$tmp = $this->get('chromedia_gaufrette.filesystem_map')->get('default');
		echo '<br/><br/>local';
		var_dump($tmp);
		
		$tmp = $this->get('chromedia_gaufrette.filesystem_map')->get('test');
		echo '<br/><br/>test';
		var_dump($tmp);
		
		
		echo '<br/><br/>';
		//echo $filesystems->get('test')->read('file2.txt');
		$file = new File('file2.txt', $filesystems->get('test'));
		echo $file->getContent();

		
		$file = new File('helloFile', $filesystems->get('default'));
		$file->setContent('Hello World');
		
		echo $file->getContent(); // Hello World		
		
		exit;
	}
}
