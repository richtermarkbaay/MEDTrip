<?php
namespace HealthCareAbroad\MailerBundle\Services;

use HealthCareAbroad\MailerBundle\Entity\MailStatuses;

use HealthCareAbroad\MailerBundle\Entity\MailQueue;

use Doctrine\Bundle\DoctrineBundle\Registry;

use HealthCareAbroad\MailerBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MailerQueue
{
    const MAIL_MAX_ATTEMPT = 5;
    
    /**
     * @var ContainerInterface
     */
    private $container;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @param ContainerInterface $container
     */
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
        $messageQueue->setSendAt(new \DateTime($sendDate));
        $messageQueue->setFailedAttempts(0);
        $messageQueue->setCreatedAt(new \DateTime('now'));
        
        $em = $this->doctrine->getEntityManager();
        $em->persist($messageQueue);
        $em->flush();
        
        return $this;
    }
    
    /**
     * Remove a queued message
     * 
     * @param MailQueue $messageQueue
     */
    public function remove(MailQueue $messageQueue)
    {
        $em = $this->doctrine->getEntityManager();
        $em->remove($messageQueue);
        $em->flush();
    }
    
    /**
     * Get all mails in queue that are ready for sending. 
     * Mails ready for sending are flagged as either PENDING or FAILED with sendAt date less than CURRENT_TIMESTAMP
     * 
     * @return array MailerQueue
     */
    public function getMailsReadyForSending()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $result = $qb->select('a')
            ->from('MailerBundle:MailQueue', 'a')
            ->where('a.status = :pending_status')
            ->andWhere('a.sendAt <= CURRENT_TIMESTAMP()')
            ->setParameter('pending_status', MailStatuses::PENDING)
            ->getQuery()
            ->getResult();
        
        return $result;
    }

    
    /**
     * 
     * @param array $ids
     * @return void|multitype:
     */
    public function incrementFailedAttemptsByIds($ids = array())
    {
        if(!count($ids)) return;

        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $result = $qb->select('a')
            ->update('MailerBundle:MailQueue', 'a')
            ->where($qb->expr()->in('a.id', $ids))
            ->set('a.failedAttempts', 'a.failedAttempts + 1')
            ->set('a.status', MailStatuses::PENDING)
            ->getQuery()
            ->getResult();
    
        return $result;
    }


    /**
     * 
     * @param array $ids
     * @return void|multitype:
     */
    public function deleteMailsByIds(array $ids = array())
    {
        if(!count($ids)) return;
        
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $result = $qb->select('a')
            ->delete('MailerBundle:MailQueue', 'a')
            ->where($qb->expr()->in('a.id', $ids))
            ->getQuery()
            ->getResult();
    
        return $result;
    }
}