<?php

namespace HealthCareAbroad\HelperBundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * 
 * @author Allejo Chris G. Velarde
 */
class SyncCitiesFromGlobalDataCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('location:syncCities')
            ->setDescription('This script will just populate geo_city_id');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->em = $this->doctrine->getManager();
        $this->connection = $this->doctrine->getConnection();
        
        $this->_populateGeoCityIdField($output);
    }
    
    private function _populateGeoCityIdField(OutputInterface $output)
    {
        // get all current cities
        $currentCities = $this->doctrine->getRepository('HelperBundle:City')->findAll();
        $output->writeln("Updating ".count($currentCities).' cities: ');
        foreach ($currentCities as $_city) {
            if (!$_city->getOldId()){
                continue;
            }
            $output->write("Syncronize {$_city->getName()}: ");
            if ($_city->getGeoCityId()) {
                $output->writeln("DONE");
                continue;
            }
        
            $sql = "SELECT cg_gct.* FROM `chromedia_global`.`geo_cities` cg_gct WHERE cg_gct.`__old_city_id` = {$_city->getOldId()} LIMIT 1";
            $statement = $this->connection->prepare($sql);
            $statement->execute();
        
            $globalCityData = $statement->fetch();
            if ($globalCityData) {
                // update
                $_city->setGeoCityId($globalCityData['id']);
                $this->em->persist($_city);
                $output->writeln("OK");
            }
            else {
                $output->writeln("NO MATCH");
            }
            $this->em->flush();
        }
    }
}