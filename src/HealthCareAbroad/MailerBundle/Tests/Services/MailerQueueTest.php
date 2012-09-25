<?php
namespace HealthCareAbroad\MailerBundle\Tests\Services;

use HealthCareAbroad\MailerBundle\Services\MailerQueue;

use HealthCareAbroad\MailerBundle\Services\MessageService;

use HealthCareAbroad\MailerBundle\Tests\MailerBundleUnitTestCase;

class MailerQueueTest extends MailerBundleUnitTestCase
{
    public function testAdd()
    {
        $messageService = new MessageService($this->getServiceContainer());
        $message = $messageService->createMessage()
            ->addFrom('errors2@chromedia.com', 'eee')
            ->setFrom('errors@chromedia.com')
            //->setFrom(array('errors3@chromedia.com', 'errors4@chromedia.com' => 'wata'))
            ->addTo('chris.velarde@chromedia.com','Chris Velarde')
            ->setTo(array('a@chromedia.com', 'b@chromedia.com' => 'Bbbb'))
            ->setBody('watadf adsfjdsflkds', 'text/html')->addPart('watadf adsfjdsflkds', 'text/plain');
        
        $mailerQueue = new MailerQueue($this->getServiceContainer());
        $mailerQueue->add($message);
        
    }
}