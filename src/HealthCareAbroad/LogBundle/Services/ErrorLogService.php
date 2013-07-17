<?php
namespace HealthCareAbroad\LogBundle\Services;

use HealthCareAbroad\LogBundle\Entity\ErrorType;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\LogBundle\Entity\ErrorLog;

/**
 * Service handling the ErrorLog entity class
 *
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class ErrorLogService implements ExceptionLogger
{
    private $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function logException(\Exception $e)
    {
        $errorLog = new ErrorLog();
        $errorLog->setErrorType(ErrorType::EXCEPTION);
        $errorLog->setMessage($e->getMessage());
        $errorLog->setStacktrace($e->getTraceAsString());
        $errorLog->setHttpUserAgent(isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $errorLog->setRemoteAddress(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '');
        $errorLog->setServerJSON(\json_encode($_SERVER));

        $em = $this->doctrine->getEntityManager('logger');
        if (!$em->isOpen()) {
            $this->doctrine->resetEntityManager('logger');
            $em = $this->doctrine->getEntityManager('logger');
        }

        $em->persist($errorLog);
        $em->flush(); // save log
    }
}