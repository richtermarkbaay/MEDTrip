<?php

namespace HealthCareAbroad\HelperBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCitiesDataFromGlobalDataCommand extends ContainerAwareCommand
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    protected function configure()
    {
        $this->setName('location:updateCitiesData')->setDescription('Update cities.id, cities.name and cities.slug from chromedia global');
        
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        
        // get all cities
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('ct')
            ->from('HelperBundle:City', 'ct')
            ->where('ct.geoCityId IS NOT NULL')
            ->orderBy('ct.id', 'ASC');
        $cities = $qb->getQuery()->getResult();
        
        $connection =  $this->doctrine->getConnection();
        
        
        foreach ($cities as $city) {
            $output->write('Updating data of old city #'.$city->getId());
            $sql = "SELECT cg_gct.* FROM `chromedia_global`.`geo_cities` cg_gct WHERE cg_gct.`id` = ?";
            $statement = $connection->prepare($sql);
            $statement->bindValue(1, $city->getGeoCityId());
            $statement->execute();
            
            $globalCityData = $statement->fetch();
            if ($globalCityData) {
                $updateSql = "UPDATE `cities` SET `id` = :geoCityId, `name` = :geoCityName, `slug` = :geoCitySlug WHERE `old_id` = :oldId";
                $updateStatement = $connection->prepare($updateSql);
                $updateStatement->bindValue('geoCityId', $globalCityData['id']);
                $updateStatement->bindValue('geoCityName', $globalCityData['name']);
                $updateStatement->bindValue('geoCitySlug', $globalCityData['slug']);
                $updateStatement->bindValue('oldId', $city->getOldId());
                $updateStatement->execute();
                
                $output->writeln(' OK');
            }
            else {
                $output->writeln(' Not found');
            }
               
        }
    }
}