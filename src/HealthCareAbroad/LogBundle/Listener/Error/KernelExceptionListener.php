<?php
/**
 * Listener kernel exception event. This will only log the exception in the database
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\LogBundle\Listener\Error;

use HealthCareAbroad\LogBundle\Entity\ErrorType;

use HealthCareAbroad\LogBundle\Entity\ErrorLog;

use HealthCareAbroad\LogBundle\Repository\ErrorLogRepository;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class KernelExceptionListener
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var ErrorLogRepository
     */
    private $repository;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('LogBundle:ErrorLog');
    }
    
    /**
     * listener for kernel.exception
     * 
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->logException($exception);
        
        // do nothing since we only want to save this to database
    }
    
    public function logException(\Exception $exception)
    {
        $errorLog = new ErrorLog();
        $errorLog->setErrorType(ErrorType::EXCEPTION);
        $errorLog->setMessage($exception->getMessage());
        $errorLog->setStacktrace($exception->getTraceAsString());
        $errorLog->setHttpUserAgent(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $errorLog->setRemoteAddress(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $errorLog->setServerJSON(\json_encode($_SERVER));
        
        //TODO: note to self, use a separate entity manager here
        $em = $this->doctrine->getEntityManager();
        if (!$em->isOpen()) {
            $this->doctrine->resetEntityManager();
            $em = $this->doctrine->getEntityManager();
        }

        $em->persist($errorLog);
        $em->flush(); // save log
    }
}