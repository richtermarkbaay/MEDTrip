<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use Doctrine\Tests\ORM\Proxy\SleepClass;
use Assetic\Exception\Exception;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptCleanUpWebsitesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadWebsites')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $results = $em->getRepository('InstitutionBundle:Institution')->findAll();
        
        $defaultValue = array();
        foreach($results as $institution) {
            echo "id:" .$institution->getId(). "\n";
            $oldData =  $institution->getWebsiteBackUp();
            $websitesArray = json_decode(\stripslashes($oldData), true);
            
            if (\is_array($websitesArray)) {
                if(isset($websitesArray['main'])) {
                    $institution->setWebsites(isset($websitesArray['main']) ? $websitesArray['main'] : '' );
                    if($institution->getSocialMediaSites() == ''){
                        $defaultValue['facebook'] = isset($websitesArray['facebook']) ? $websitesArray['facebook'] : '';
                        $defaultValue['twitter'] = isset($websitesArray['twitter']) ? $websitesArray['twitter'] : '';
                        $defaultValue['googleplus'] = '';
                        $institution->setSocialMediaSites(\json_encode($defaultValue));
                        
                        echo "SocialMediaSites" .$institution->getSocialMediaSites()."\n";
                    }
                    
                    $em->persist($institution);
                    echo "new:" .$institution->getWebsites(). "\n";
                }
            }
            
        }
        $em->flush();
        
        echo "done\n";
        exit;
    }
    
}