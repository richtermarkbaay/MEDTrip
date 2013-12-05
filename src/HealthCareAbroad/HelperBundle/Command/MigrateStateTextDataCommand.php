<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Entity\State;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Doctrine\ORM\Query;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class MigrateStateTextDataCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    private $nonUniqueStates = array();
    
    private $nonMatchingInstitutions = array();
    
    /**
     * @var LocationService
     */
    private $locationService;
    
    protected function configure()
    {
        $this->setName('helper:migrateStateTextData')
            ->setDescription('Convert state text data to corresponding state id.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->locationService = $this->getContainer()->get('services.location');
        $this->migrateInstitutionStateData();
    }
    
    protected function migrateInstitutionStateData()
    {
        $result = $this->getInstitutionsWithNoState();
        $em = $this->doctrine->getManager();
        foreach ($result as $institution) {
            $this->doInstitution($institution, $em);
        }
        $em->flush();
        
        $this->output->writeln('DONE. Get summary for non matching '.\count($this->nonMatchingInstitutions).' institution states');
        $this->output->writeln("==============================================");
        $this->output->writeln("");
        $this->output->writeln("");
        $router = $this->getContainer()->get('router');
        
        foreach ($this->nonMatchingInstitutions as $institution){
            $this->output->writeln("-------------");
            $this->output->writeln("ID: {$institution->getId()}");
            $this->output->writeln("Name: {$institution->getName()}");
            $url = $router->generate('admin_institution_view', array('institutionId' => $institution->getId()), true);
            $this->output->writeln("Admin URL: ".$url);
            $this->output->writeln("Old state: ".$institution->getStateBak());
            $this->output->writeln("-------------");
            $this->output->writeln("");
        }
    }
    
    private function getInstitutionsWithNoState()
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('inst')
        ->from('InstitutionBundle:Institution', 'inst')
        ->where('inst.state IS NULL')
        ;
        $result = $qb->getQuery()->getResult();
        return $result;
    }
    
    private function doInstitution(Institution $institution, $em)
    {
        //$this->output->write("Institution #{$institution->getId()}");
        $oldState = \trim($institution->getStateBak());
        if ($oldState == ''){
            //$this->output->writeln('[No old state]');
            return;
        }
        $country = $institution->getCountry();
        
        // find a state with the same name for this country in chromedia_global
        $connection = $this->doctrine->getConnection();
        $sql = "SELECT gs.* FROM `chromedia_global`.`geo_states` gs WHERE `name` LIKE :state AND `geo_country_id` = :geoCountryId";
        $statement = $connection->prepare($sql);
        $statement->bindValue('state', $oldState);
        $statement->bindValue('geoCountryId', $country->getId());
        $statement->execute();
        if (!$rowCount = $statement->rowCount()){
            //$this->output->writeln('[No matching state]');
            $this->nonMatchingInstitutions[] = $institution;
            return;
        }
        else {
            if ($rowCount == 1){
                $globalStateData = $statement->fetch();
                $hcaState = $this->getHcaState($globalStateData, $country);
                $institution->setState($hcaState);
                $em->persist($institution);
                
                //$this->output->writeln("[Set to {$hcaState->getName()} ]");
            }
            else {
                if (strtolower($oldState) == 'singapore'){
                    // manual for singapore
                    while ($globalStateData = $statement->fetch()){
                        if ($globalStateData['id'] == 2732){
                            $hcaState = $this->getHcaState($globalStateData, $country);
                            $institution->setState($hcaState);
                            $em->persist($institution);
                            
                            //$this->output->writeln("[Set to {$hcaState->getName()} ]");
                            break;
                        }
                    }
                }
                else {
                    $this->nonUniqueStates[] = array('country_id' => $country->getId(), 'state' => $oldState);
                    //$this->output->writeln("[Non unique state {$oldState}]");
                    $this->nonMatchingInstitutions[] = $institution;
                }
                
            }
            
        }
    }
    
    /**
     * 
     * @param array $globaStateData
     * @return State
     */
    private function getHcaState($globalStateData, Country $country)
    {
        $hcaState = $this->locationService->getStateById($globalStateData['id']);
        if (!$hcaState) {
            // create new hca state
            $hcaState = new State();
            $hcaState->setId($globalStateData['id']);
            $hcaState->setName($globalStateData['name']);
            $hcaState->setCountry($country);
        }
        
        return $hcaState;
    }
    
    
}