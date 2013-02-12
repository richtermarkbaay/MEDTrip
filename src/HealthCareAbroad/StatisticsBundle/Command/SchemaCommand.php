<?php

namespace HealthCareAbroad\StatisticsBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SchemaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('statistics:schema')->setDescription('Prepare needed schema for statistics');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
    }
}