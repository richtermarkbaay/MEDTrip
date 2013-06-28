<?php

namespace HealthCareAbroad\MailerBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class TestSendMailCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('mailer:testSendMail')
            ->setDescription('Sends a test email');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = \Swift_Message::newInstance();
        $message->setSubject('test only')
            ->setSender('acgvelarde@gmail.com', 'acgvelarde@gmail.com')
            ->setTo('chris.velarde@chromedia.com', 'chris.velarde@chromedia.com')
            ->setBody('test only');
        
        
        $output->writeln('Sending message to '.implode(', ',$message->getTo()).' '.implode(', ',$message->getSender()));
        
        $result = $this->getContainer()->get('mailer')->send($message);
        $output->writeln('Output: '.$result);
    }
}