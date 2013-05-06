<?php
namespace HealthCareAbroad\HelperBundle\Twig;
class InlineJavascriptTwigExtension extends \Twig_Extension
{
	
	private static $inlineCode='';
	
	private static $inline_files = array();
	
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
            'render_media_file_script'=> new \Twig_Function_Method($this, 'renderMediaFileScript'),
            'add_javascript_file' => new \Twig_Function_Method($this, 'add_javascript_file'),
            'render_javascript_files' => new \Twig_Function_Method($this, 'render_javascript_files'),
            'include_js_files' => new \Twig_Function_Method($this, 'includeJsFiles')
		);
	}
	
	public function getTemplateContents($templateName)
	{
		$s = $this->twig->render($templateName);
		return \preg_replace('/\s+/', ' ', $s);
	}
	
	public function add_javascript_file($src)
	{
	    static::$inline_files[] = $src;
	}
	
	public function render_javascript_files()
	{
	    if (\count(static::$inline_files)) {
	        $s = '<script type="text/javascript" src="'.\implode('"></script><script type="text/javascript" src="', static::$inline_files).'"></script>';
	        return $s;
	    }
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
	
	public function includeJsFiles($files)
	{
	    static $hasRendered =false;
	    if (!$hasRendered) {
	        $hasRendered = true;
	        return '<script type="text/javascript" src="'.\implode('"></script><script type="text/javascript" src="', $files).'"></script>';
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
