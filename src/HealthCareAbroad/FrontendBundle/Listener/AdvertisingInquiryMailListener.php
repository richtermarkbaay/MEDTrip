<?php

namespace HealthCareAbroad\FrontendBundle\Listener;

use HealthCareAbroad\AdminBundle\Entity\Inquiry;

use HealthCareAbroad\FrontendBundle\Event\InquiryEvent;

class AdvertisingInquiryMailListener
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    /** 
     * @var string
     */
    private $siteName;
    
    
    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }
    
    public function setTwig(\Twig_Environment $v)
    {
        $this->twig = $v;
    }
    
    public function setSiteName($siteName)
    {
        $this->siteName = $siteName;
    }
    
    public function onAddInquiry(InquiryEvent $event)
    {
        $inquiry = $event->getData();
        if (!$inquiry instanceof Inquiry) {
            return; // do nothing
        }
        
        $subject = 'New ' . $this->siteName . ' Advertising Inquiry';
        $message = $this->twig->render('FrontendBundle:Mail:addInquiryMessage.txt.twig', array('inquiry' => $inquiry));
        //echo "<textarea rows='15' cols='30'>{$message}</textarea>"; exit;
        
        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('inquiries@healthcareabroad.com')
            ->setTo('sales@healthcareabroad.com')
            ->addBcc('chris.velarde@chromedia.com')
            ->setBody($message);
        
        $this->mailer->send($message);
    }
}