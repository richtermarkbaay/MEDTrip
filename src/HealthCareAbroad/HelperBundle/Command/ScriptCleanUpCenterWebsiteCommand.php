<?php

namespace HealthCareAbroad\HelperBundle\Command;

use Doctrine\Tests\ORM\Proxy\SleepClass;
use Assetic\Exception\Exception;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptCleanUpCenterWebsiteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadClinicWebsites')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $results = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findAll();
        
        $defaultValue = array();
        foreach($results as $center) {
            echo "center id:" .$center->getId(). "\n";
            $oldData =  $center->getWebsiteBackUp();
            $websitesArray = json_decode(\stripslashes($oldData), true);
            
            if (\is_array($websitesArray)) {
                if(isset($websitesArray['main'])) {
                    $center->setWebsites(isset($websitesArray['main']) ? $websitesArray['main'] : '' );
                    if($center->getSocialMediaSites() == ''){
                        $defaultValue['facebook'] = isset($websitesArray['facebook']) ? $websitesArray['facebook'] : '';
                        $defaultValue['twitter'] = isset($websitesArray['twitter']) ? $websitesArray['twitter'] : '';
                        $defaultValue['googleplus'] = '';
                        $center->setSocialMediaSites(\json_encode($defaultValue));
                        
                        echo "socialMediaSites" .$center->getSocialMediaSites()."\n";
                    }
                    
                    $em->persist($center);
                    echo "new:" .$center->getWebsites(). "\n";
                }
            }
            
        }
        $em->flush();
        
        echo "done\n";
        exit;
    }
    
}