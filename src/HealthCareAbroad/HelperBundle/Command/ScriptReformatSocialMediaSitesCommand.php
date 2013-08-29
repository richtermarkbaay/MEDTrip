<?php
/**
* @author Adelbert Silla
*/
namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptReformatSocialMediaSitesCommand extends ContainerAwareCommand
{
    /**
    * @var OutputInterface
    */
    private $output;
    
    const FACEBOOK_URL = 'https://facebook.com/';
    const TWITTER_URL = 'https://twitter.com/';
    const GOOGLEPLUS_URL = 'https://plus.google.com/';

    protected function configure()
    {
        $this->setName('script:ReformatSocialMediaSites')->setDescription('Reformat Institution/InstitutionMedicalCenter Social Media Sites');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $em = $this->getContainer()->get('doctrine')->getEntityManager();


        // Reformat Institution SocialMediaSites
        $institutions = $em->getRepository('InstitutionBundle:Institution')->findAll();
        $this->reformatSocialMediaSites($institutions, 'Institution');


        // Reformat InstitutionMedicalCenter SocialMediaSites
        $institutionMedicalCenters = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findAll();
        $this->reformatSocialMediaSites($institutionMedicalCenters, 'Center');
        
        $this->output->writeln('Total Institution: ' .  count($institutions));
        $this->output->writeln('Total InstitutionMedicalCenter: ' . count($institutionMedicalCenters));
        $this->output->writeln('done');
    }
    
    protected function reformatSocialMediaSites($results, $objectType)
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();

        foreach($results as $each) {
            $hasFormattedSites = false;
            $socialMediaSitesArr = \json_decode($each->getSocialMediaSites(), true);
        
            $this->output->writeln("$objectType ID: " . $each->getId());
        
            // IF not valid data or empty.
            if(!$socialMediaSitesArr || !is_array($socialMediaSitesArr)) {
                $each->setSocialMediaSites(\json_encode(SocialMediaSites::getDefaultValues()));
                $em->persist($each);
                continue;
            }
        
            foreach($socialMediaSitesArr as $type => $value) {
                if($value && !filter_var($value, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
                    $hasFormattedSites = true;
                    $this->output->write("Reformat: $value");
                    if(trim($value) == 'http://') {
                        $socialMediaSitesArr[$type] = '';
                    } else {
                        $socialMediaSitesArr[$type] = $this->getSocialMediaSitesUrlByType($type) . $value;
                    }

                    $this->output->writeln(" => " . $socialMediaSitesArr[$type] . " ($type)");
                }

                // else { $this->output->writeln("Ignored url: " . $socialMediaSitesArr[$type] . " ($type)"); }
            }
        
            if($hasFormattedSites) {
                $each->setSocialMediaSites(\json_encode($socialMediaSitesArr));
                $em->persist($each);
            }
        }
        $em->flush();
    }
    
    private function getSocialMediaSitesUrlByType($type)
    {
        $urls = array(
            SocialMediaSites::FACEBOOK => self::FACEBOOK_URL,
            SocialMediaSites::TWITTER => self::TWITTER_URL,
            SocialMediaSites::GOOGLEPLUS => self::GOOGLEPLUS_URL
        );
        
        return isset($urls[$type]) ? $urls[$type] : ''; 
    } 
}