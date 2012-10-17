<?php
/**
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptCheckerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:checker')->setDescription('Check scripts');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $commandScripts = $em->getRepository('HelperBundle:CommandScriptLog')->findByStatus(CommandScriptLog::STATUS_ACTIVE);

        foreach($commandScripts as $script) {
            
            if($script->getAttempts() > CommandScriptLog::MAX_ATTEMPT) {
                $output->writeln($script->getAttempts());
                // send email

            } else {

               if(!$this->checkScriptIfStillRunning($script->getScriptName())) {

                    $output->writeln("rerun " . $script->getScriptName());

                    $result = $this->runScript($script->getScriptName());
                    $output->writeln($result);
               }

                //$script->setLastDateCompleted(new \DateTime());
            }
        }

        // Print end of script
        $output->writeln('end of ' . $this->getName() . ' script');
    }

    /**
     * 
     * @param unknown_type $scriptName
     * @return string
     */
    private function runScript($scriptName = '')
    {
        if(!$scriptName) return;

        return shell_exec("app/console $scriptName > /dev/null &");
    }


    /**
     * 
     * @param unknown_type $scriptName
     * @return boolean
     */
    private function checkScriptIfStillRunning($scriptName)
    {
        if(!$scriptName) return false;

        $output = shell_exec("ps aux|grep $scriptName");
        $arrOutput = explode("\n", trim($output));

        print($output);

        return count($arrOutput) > 2 ? true : false;
    }
}