<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Services\LocationService;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class UpdateCouchDbCitiesCommand extends ContainerAwareCommand
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @var LocationService
     */
    private $locationService; 

    protected function configure()
    {
        $this->setName('script:updateCouchDbCities')->setDescription('Update CouchDb Cities Database.');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->locationService = $this->getContainer()->get('services.location');


        // Fetching GlobalCountries form ChromediaApi
        $this->output->write("\nFetching countries from ChromediaApi... ");
        $countries = $this->locationService->getGlobalCountries();        
        $this->output->writeln(count($countries['data']) . " countries\n");


        $connection =  $this->getContainer()->get('doctrine')->getConnection();        

        foreach($countries['data'] as $id => $each) {

            // Query get_cities By countryId
            $sql = "SELECT a.* FROM `chromedia_global`.`geo_cities` a WHERE a.`geo_country_id` = :countryId and a.`status` = :status ORDER BY a.name ASC";
            $statement = $connection->prepare($sql);
            $statement->bindValue('countryId', $id);
            $statement->bindValue('status', 1);
            $statement->execute();

            // Initialize document $data to be saved.
            $cities = array();


            $globalCitiesData = $statement->fetchAll();
            foreach($globalCitiesData as $city) {
                $cities[$city['id']] = array('id' => $city['id'], 'name' => $city['name'], 'slug' => $city['slug']);
            }

            $data['data']['data'] = $cities;
            $data['data']['country'] = $each;
            $data['data']['totalResults'] = count($globalCitiesData);

            $citiesDocId = "country_$id";
            $citiesDoc = \json_decode($this->locationService->couchDbService->get("country_$id"), true);
            
            if($citiesDoc) {
                $data['_rev'] = $citiesDoc['_rev']; 
                $this->output->write("Updating cities: $citiesDocId (rev: " . $data['_rev'] . ') ... ');
            } else {
                $this->output->write("Adding new cities: $citiesDocId ... ");
            }

            $result = $this->locationService->couchDbService->put($citiesDocId, $data);

            if(isset($result['id']) && isset($result['rev'])) {
                $this->output->writeln('SAVED - count: ' . $data['data']['totalResults'] );
            } else {
                $this->output->writeln('FAILED.');
            }
        }

        $this->output->writeln("\nEnd of Script.");
    }
    
}