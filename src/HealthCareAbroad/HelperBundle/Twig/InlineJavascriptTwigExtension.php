<?php
namespace HealthCareAbroad\HelperBundle\Twig;
class InlineJavascriptTwigExtension extends \Twig_Extension
{
	
	private static $inlineCode='';
	
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	public function setTwig($twig)
	{
		$this->twig = $twig;
	}
	
	public function getFunctions()
	{
		return array(
			'getTemplateContents' => new \Twig_Function_Method($this, 'getTemplateContents'),
			'outputInlineJavascript' => new \Twig_Function_Method($this, 'outputInlineJavascript'),
			'addInlineJavascript' => new \Twig_Function_Method($this, 'addInlineJavascript'),
            'render_media_file_script'=> new \Twig_Function_Method($this, 'renderMediaFileScript')
		);
	}
	
	public function getTemplateContents($templateName)
	{
		return $this->twig->render($templateName);
	}
	
	public function addInlineJavascript($code)
	{
		self::$inlineCode .= $code;
	}
	
	public function renderMediaFileScript()
	{
	    static $hasRendered =false;
	    if (!$hasRendered) {
	        $hasRendered = true;
	        return $this->twig->render('HelperBundle:InlineJavascript:media.file.js.twig');
	    }
	}
	
	public function outputInlineJavascript(){
		return self::$inlineCode;
	}
	
	public function getName()
	{
		return 'inlineJavascript';
	}
}
