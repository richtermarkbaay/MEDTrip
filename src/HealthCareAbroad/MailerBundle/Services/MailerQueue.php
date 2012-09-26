<?php
namespace HealthCareAbroad\MailerBundle\Services;

use HealthCareAbroad\MailerBundle\Entity\MailStatuses;

use HealthCareAbroad\MailerBundle\Entity\MailQueue;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\MailerBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerQueue
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->doctrine = $this->container->get('doctrine');
    }
    
    /**
     * Add a Message to queue
     * 
     * @param Message $message
     * @param datetime $sendDate valid date/time string format when this message will be sent
     * @return \HealthCareAbroad\MailerBundle\Services\MailerQueue
     */
    public function add(Message $message, $sendDate=null)
    {
        $serializedData = \serialize($message);
        
        $messageQueue = new MailQueue();
        $messageQueue->setMessageData($serializedData);
        $messageQueue->setStatus(MailStatuses::PENDING);
        $messageQueue->setSendAt($sendDate);
        $messageQueue->setFailedAttempts(0);
        $messageQueue->setCreatedAt(new \DateTime($sendDate));
        
        $em = $this->doctrine->getEntityManager();
        $em->persist($messageQueue);
        $em->flush();
        
        return $this;
    }
}