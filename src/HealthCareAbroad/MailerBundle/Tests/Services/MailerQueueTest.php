<?php
namespace HealthCareAbroad\MailerBundle\Tests\Services;

use HealthCareAbroad\MailerBundle\Entity\MailQueue;

use HealthCareAbroad\MailerBundle\Entity\MailStatuses;

use HealthCareAbroad\MailerBundle\Services\MailerQueue;

use HealthCareAbroad\MailerBundle\Services\MessageService;

use HealthCareAbroad\MailerBundle\Tests\MailerBundleUnitTestCase;

class MailerQueueTest extends MailerBundleUnitTestCase
{
    /**
     * @var MailerQueue
     */
    private $mailerQueue;

    /**
     * @var MessageService
     */
    private $messageService;

    public function setUp()
    {
        $this->markTestSkipped('MailerQueue temporarily disabled.'); return;

        $this->messageService = new MessageService($this->getServiceContainer());
        $this->mailerQueue = new MailerQueue($this->getServiceContainer());
        $this->mailer = $this->getServiceContainer()->get('mailer');
    }

    private function _getLatestSavedQueue()
    {
        $query = $this->getDoctrine()->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('MailerBundle:MailQueue', 'a')
            ->orderBy('a.id', 'desc')
            ->setMaxResults(1)
            ->getQuery();
        return $query->getOneOrNullResult();
    }

    public function testSend()
    {
        $message = $this->messageService->createMessage()
            ->setFrom('test@chromedia.com')
            ->setTo(array('harold.modesto@chromedia.com'))
            ->setBody('<b>text/html</b>', 'text/html')->addPart('text/plain', 'text/plain');

        $sentEmails = 0;

        if (!$sentEmails = $this->mailer->send($message, $failures))
        {
            echo "Failures:";
            print_r($failures);
        }

        $this->assertEquals(1, $sentEmails);
    }

    public function testAdd()
    {
        $message = $this->messageService->createMessage()
            ->setFrom('test@chromedia.com')
            ->addFrom('errors2@chromedia.com', 'eee')
            ->setTo(array('a@chromedia.com', 'b@chromedia.com' => 'Bbbb'))
            ->addTo('chris.velarde@chromedia.com','Chris Velarde')
            ->setCc(array('cc1@chromedia.com', 'cc2@chromedia.com' => 'cc2 ini'))
            ->setBcc(array('bcc1@chromedia.com', 'bcc2@chromedia.com' => 'bcc2 ini'))
            ->setBody('watadf adsfjdsflkds', 'text/html')->addPart('watadf adsfjdsflkds', 'text/plain');


        $this->mailerQueue->add($message);

        // get the last message saved
        $messageQueue = $this->_getLatestSavedQueue();

        $this->assertInstanceOf('HealthCareAbroad\MailerBundle\Entity\MailQueue', $messageQueue);

        $queueMessageData = \unserialize($messageQueue->getMessageData());
        $this->assertEquals($message->getFrom(), $queueMessageData->getFrom(), 'Expecting FROM data to be the same');
        $this->assertEquals($message->getTo(), $queueMessageData->getTo(), 'Expecting TO data to be the same');
        $this->assertEquals($message->getCc(), $queueMessageData->getCc(), 'Expecting CC data to be the same');
        $this->assertEquals($message->getBcc(), $queueMessageData->getBcc(), 'Expecting BCC data to be the same');
        $this->assertEquals($message->getBody(), $queueMessageData->getBody(), 'Expecting BODY data to be the same');

    }

    public function testGetMailsReadyForSending()
    {
        $message = $this->messageService->createMessage()
            ->setFrom('aaa@chromedia.com', 'aaa')
            ->setTo('bbb@chromedia.com', 'bbb')
            ->setSubject('test')
            ->setBody('wata');

        // add a message that can be sent 2 days from now
        $this->mailerQueue->add($message, '+2 days');

        // add a message that should be sent 1 day ago
        $this->mailerQueue->add($message, '-1 days');

        $dateNow = new \DateTime('now');

        $mails = $this->mailerQueue->getMailsReadyForSending();

        foreach ($mails as $eachMail) {
            $this->assertTrue($eachMail->getSendAt() < $dateNow, "Send at is expected to be less than current time");
            $this->assertNotEquals(MailStatuses::SENT, $eachMail->getStatus(), "Mail with SENT status must not be included in mails ready for sending");
        }
    }

    public function testRemove()
    {
        $message = $this->messageService->createMessage()
            ->setFrom('aaa@chromedia.com', 'aaa')
            ->setTo('bbb@chromedia.com', 'bbb')
            ->setSubject('test')
            ->setBody('wata');
        $this->mailerQueue->add($message);

        $lastMessageQueue = $this->_getLatestSavedQueue();
        $lastMessageQueueId = $lastMessageQueue->getId();
        $this->assertGreaterThan(0, $lastMessageQueueId);
        $this->mailerQueue->remove($lastMessageQueue);

        $this->assertNull($this->getDoctrine()->getRepository('MailerBundle:MailQueue')->find($lastMessageQueueId), 'Expecting item to be deleted already');
    }
}