<?php
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\MailerBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputOption;

use Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SenderCommand extends ContainerAwareCommand
{
    protected function configure()
    {

        $this->setName('mailer:sender')->setDescription('Test mail sender');

        //->get('services.mailer.queue')->getMailsReadyForSending()
        

//             ->addArgument('name', InputArgument::OPTIONAL, 'Who do you want to greet?')
//             ->addOption('yell', null, InputOption::VALUE_NONE, 'If set, the task will yell in uppercase letters');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mails = $this->getContainer()->get('services.mailer.queue')->getMailsReadyForSending();
        $mailer = $this->getContainer()->get('mailer');
        foreach($mails as $each) {
            $data = \unserialize($each->getMessageData());
            $x = $mailer->send($data);
            $output->writeln('result: ' . $x);
        }

        $output->writeln('end');
    }
}