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

class ScriptCleanupAlertCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('alerts:cleanup')->setDescription('Remove old alerts.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // c52fa832152690a8c44fb0dcd9000431
        //$id = 'c52fa832152690a8c44fb0dcd9000431';
//         $id = '1ff6a719cf990579f6bd9dceddf3447a';
//         $rev = '2-d352f95cc9db0066974566ed7f0a7335';

//         $alertService = $this->getContainer()->get('services.alert');
        
//         $result = $this->getContainer()->get('services.alert')->delete($id, $rev);
        
//         sleep(5);
    }

}