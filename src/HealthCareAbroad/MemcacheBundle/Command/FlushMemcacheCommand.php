<?php

namespace HealthCareAbroad\MemcacheBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class FlushMemcacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        return $this->setName('memcache:flush')
            ->setDescription('Invalidates all stored data in memcache');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $memcache = $this->getContainer()->get('services.memcache');
        $memcache->flush();
        $output->writeln('Memcache Flushed');
    }
}