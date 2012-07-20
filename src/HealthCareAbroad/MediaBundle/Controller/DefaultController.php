<?php
namespace HealthCareAbroad\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Reponse;

use Gaufrette\File;

class DefaultController extends Controller
{
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
