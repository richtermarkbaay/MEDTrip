<?php
/**
 * @author Alnie Jacobe
*/

namespace HealthCareAbroad\HelperBundle\Command;

use Doctrine\Tests\ORM\Proxy\SleepClass;

use HealthCareAbroad\HelperBundle\Entity\City;

use Assetic\Exception\Exception;

use HealthCareAbroad\HelperBundle\Entity\Country;

use Symfony\Component\HttpFoundation\File\File;
use HealthCareAbroad\HelperBundle\Services\LocationService;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptLoadCityCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadCity')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $cityFilePath = '';
        if(!$cityFilePath) {
            $cityFilePath = $this->getContainer()->getParameter('city_file_path');
        }
        $file = new File($cityFilePath);
        $contents = file_get_contents($file);
        $fileContents = explode("\n", trim($contents));
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $countryRepo = $em->getRepository('HelperBundle:Country');
        
        echo "Memory: ".\memory_get_usage(). "\n";
        
        $i = 0;
        $countries = array();
        $countriesResult = $countryRepo->findAll();
        
        foreach($countriesResult as $each) {
            $countries[$each->getAbbr()] = $each->getId();
        }
        
        $finishedNames = array();
        $ctr = 0;
        $count = count($fileContents);

        $subQuery = '';
        for($i=0; $i<$count;$i++) {
            $lineContent = $fileContents[$i];

            $line = explode(',"', trim($lineContent));

            $country_abbr = substr($line[1], 0, -1);
            $city_name = substr($line[3], 0, -1);
            if(!$city_name || !$country_abbr) {
                continue;
            }
                
            if(isset($countries[$country_abbr])) {

                $subQuery .= "(". $countries[$country_abbr] .", '". addslashes($city_name) ."', 1),";
                
                if(($ctr == 40000)|| ($count-1) == $i) {
                   $subQuery = substr($subQuery, 0 , -1) . " ON DUPLICATE KEY UPDATE status=1";
                   $sqlQuery = "INSERT INTO cities (country_id, name , status) VALUES $subQuery";
                    $output->writeln($sqlQuery); 
                    $output->writeln("\n");
                    $conn = $em->getConnection();
                    
                    $conn->executeQuery($sqlQuery);
                    $conn->close();
                    $sqlQuery = $subQuery = '';
                    $ctr = 0;
                }
            }

            $ctr++;
            echo "Memory: ".\memory_get_usage(). "\n";
        }
        echo "done\n";
        exit;
    
    }
    
}