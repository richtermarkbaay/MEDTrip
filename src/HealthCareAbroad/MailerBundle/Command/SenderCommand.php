<?php
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\MailerBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;

use HealthCareAbroad\MailerBundle\Services\MailerQueue;

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
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        // Update Script Log
        $commandScript = $em->getRepository('HelperBundle:CommandScriptLog')->findOneByScriptName($this->getName());
        $commandScript->setLastRunDate(new \DateTime())->setStatus(CommandScriptLog::STATUS_ACTIVE);
        $em->persist($commandScript);
        $em->flush();

        $mailer = $this->getContainer()->get('mailer');
        $mailerQueueService = $this->getContainer()->get('services.mailer.queue');
        $mails = $mailerQueueService->getMailsReadyForSending();

        $sentMails = $failedMails = $deleteMails = array();
        
        foreach($mails as $each) {

            $data = \unserialize($each->getMessageData());
            $result = $mailer->send($data);

            if(!$result) {

                if($each->getFailedAttempts() >= MailerQueue::MAIL_MAX_ATTEMPT) {
                    $deleteMails[] = $each->getId(); 
                } else {
                    $failedMails[] = $each->getId(); 
                }

            } else {
                $sentMails[] = $each->getId();
            }

            $output->writeln('mail sent status with id (' . $each->getId() . '): '. $result);
        }


        // Increment failed mails attempts by 1
        $mailerQueueService->incrementFailedAttemptsByIds($failedMails);


        // Delete both sent mails and those which reached the max attempts
        $mailerQueueService->deleteMailsByIds(array_merge($deleteMails, $sentMails));        


        // Print end of script
        $output->writeln('end of script ' . $this->getName());

        sleep(1);

        $this->execute($input, $output);
    }
}