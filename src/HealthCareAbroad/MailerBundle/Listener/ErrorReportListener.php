<?php
/**
 * 
 * @author Chaztine Blance
 *
 */

namespace HealthCareAbroad\MailerBundle\Listener;

use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

class ErrorReportListener
{
	/**
	 *
	 * @var \Twig_Environment
	 */
	protected $twig;
	protected $mailer;
	protected $doctrine;
	
	public function setTwig(\Twig_Environment $twig)
	{
		$this->twig = $twig;
	}
	
	public function setMailer($mailer)
	{
		$this->mailer = $mailer;
	}
	/**
	 * @var HealthCareAbroad\AdminBundle\Services\ErrorReportUserService
	 */
	
    public function onCreate(CreateErrorReportEvent $event)
    {
  		$report = $event->getErrorReport();
     	$messageBody = $this->twig->render('HelperBundle:Email:errorReport.email.twig', array(
	    			'reporterName' => $report->getReporterName(),
	    			'details' => $report->getDetails(),
	    			'dateCreated' => $report->getDateCreated()
	    	));
	    	 
    	// send email
    	$message = \Swift_Message::newInstance()
    	->setSubject('New Error Report')
    	->setFrom('chris.velarde@chromedia.com')
    	->setTo('chaztine.blance@chromedia.com')
    	->setBody($messageBody);
    	$sendResult = $this->mailer->send($message);
    	
    	return $sendResult;
    }
}