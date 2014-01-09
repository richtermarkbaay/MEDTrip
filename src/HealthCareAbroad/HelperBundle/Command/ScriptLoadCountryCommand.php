<?php
/**
 * FIXME: Outdated script!
 * 
 * @author Alnie Jacobe
**/

namespace HealthCareAbroad\HelperBundle\Command;

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

class ScriptLoadCountryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadCountry')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $countryFilePath = '';
        if(!$countryFilePath) {
            $countryFilePath = $this->getContainer()->getParameter('country_file_path');
        }
        
        $file = new File($countryFilePath);
        $contents = file_get_contents($file);
        $fileContents = explode("\n", $contents);
        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $em->getRepository('HelperBundle:Country');
        
        echo "Memory: ".\memory_get_usage(). "\n";
        
        $countries = $this->getAllCountries('abbr'); //parameter is the field to be check in Entity Country
        $finishedNames = array();
        $code = '';
        foreach ($fileContents as $lineContent) {
            $line = explode(",", trim($lineContent));
            if(!isset($line[4]))
            {
                continue;
            }
            $abbr = str_replace('"','',$line[4]);
            $name = str_replace('"','',$line[5]);
            if($countries) {
                if (\in_array($name, $countries)) {
                    continue;
                }
            }
            
            if (\in_array($name, $finishedNames)) {
                continue;
            }    
            //persist data to db
            $country = new Country();
            $country->setAbbr($abbr);
            $country->setName($name);
            $country->setStatus(Country::STATUS_ACTIVE);
            $country->setCode($code);
            $em->persist($country);
            $finishedNames[] = $name;
            
            echo "Memory: ".\memory_get_usage(). "\n";
        }
        $em->flush();
        
        //load country code
        $this->loadCountryCode();
        echo "successfully loaded";
        exit;
        
    }
    
    private function loadCountryCode()
    {
        $countryCodeFilePath = '';
        if(!$countryCodeFilePath) {
            $countryCodeFilePath = $this->getContainer()->getParameter('country_code_file_path');
        }
        
        $file = new File($countryCodeFilePath);
        $contents = file_get_contents($file);
        $fileContents = explode("\n", $contents);
        $countryCodeArray = array();
        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $countryRepo = $em->getRepository('HelperBundle:Country');
        
        echo "Memory: ".\memory_get_usage(). "\n";
        
        $countries = $this->getAllCountries('code');
        $finishedNames = array();
        foreach ($fileContents as $lineContent) {
        
            $line = explode("=>", trim($lineContent));
            if(!isset($line[1]))
            {
                continue;
            }
            $code = str_replace("'",'',str_replace("," ,"" ,str_replace(" ", "", $line[1])));
            $name = str_replace("'",'',$line[0]);
            if($countries) {
                if (\in_array($name, $countries)) {
                    continue;
                }
            }
            
            if (\in_array($name, $finishedNames)) {
                continue;
            }
            
            //persist data to db
            $country = $countryRepo->findOneBy(array('name' => $name));
            if($country) {
                $country->setCode($code);
                $em->persist($country);
                $finishedNames[] = $name;
            }
            echo "Memory: ".\memory_get_usage(). "\n";
        }
        $em->flush();
        return;
    }
    private function getAllCountries($field)
    {
        $dql = "SELECT a.name FROM HelperBundle:Country a WHERE a." . $field . "!= :empty";
        $query = $this->getContainer()->get('doctrine')->getEntityManager()->createQuery($dql)
        ->setParameter('empty', '');
        
        $countryObj = $query->getResult();
        $countryArray = array();
        //
        foreach($countryObj as $key => $value){
            $countryArray[] = $value['name'];
        }
        return $countryArray;
    }
    
    
}