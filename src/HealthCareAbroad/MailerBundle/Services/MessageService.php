<?php
namespace HealthCareAbroad\MailerBundle\Services;

use HealthCareAbroad\MailerBundle\Entity\Message;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageService
{
    /**
     * @var ContainerInterface
     */
    private $container;
    
    public function __construct(ContainerInterface $container=null)
    {
        $this->container = $container;
    }
    
    /**
     * Create a message instance
     * 
     * @return Message
     */
    public function createMessage()
    {
        return new Message();
    }
}