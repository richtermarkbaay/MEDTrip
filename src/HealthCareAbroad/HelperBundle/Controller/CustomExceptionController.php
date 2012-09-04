<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\HelperBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

use Symfony\Component\HttpKernel\Exception\FlattenException;

use Symfony\Bundle\TwigBundle\Controller\ExceptionController;

class CustomExceptionController extends ExceptionController
{
    private $request;
    
    public function showAction(FlattenException $exception, DebugLoggerInterface $logger = null, $format='html')
    {
        // we will only customize the exception page for Non-debug environment
        if ($isDebug = $this->container->get('kernel')->isDebug()) {
            return parent::showAction($exception, $logger, $format);   
        }
        else {
            
            //TODO: there might be a case in the future that we will use other formats, but right now let's make this simple and always use an html template
            $this->request =  $this->container->get('request');
            $this->request->setRequestFormat('html');
            $currentContent = $this->getAndCleanOutputBuffering();
            
            $templating = $this->container->get('templating');
            $code = $exception->getStatusCode();
            
            return $templating->renderResponse(
                $this->findTemplate($templating, $format, $code, $isDebug),
                array(
                    'status_code'    => $code,
                    'status_text'    => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                    'exception'      => $exception,
                    'logger'         => $logger,
                    'currentContent' => $currentContent,
                )
            );
        }
    }
    
    protected function findTemplate($templating, $format, $code, $debug)
    {
        $pathInfo = $this->request->server->get('PATH_INFO');
        
        // check if this path is /admin/
        if (\preg_match('/^\/admin\//', $pathInfo)) {
            $template = new TemplateReference('AdminBundle', 'Exception', 'error', 'html', 'twig');
            
            if ($templating->exists($template)) {
                return $template;
            }
        }
        // check if path is /institution/
        elseif (\preg_match('/^\/institution\//', $pathInfo)){
            
        }
        
        // not in /admin and /institution or the specific template was not found, use default error template
        return parent::findTemplate($templating, $format, $code, $debug);
    }
}