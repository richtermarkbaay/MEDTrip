<?php

namespace HealthCareAbroad\MemcacheBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MemcacheStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('memcache:stats')
        ->setDescription('Stats for memcache');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $memcache = $this->getContainer()->get('services.memcache');
        $stats = $memcache->getExtendedStats();
        
        foreach ($stats as $server => $data){
            $output->writeln("Memcache stats for {$server}:");
            foreach ($data as $key => $value){
                $output->writeln("    {$key}: {$value}");
            }
        }
    }
}