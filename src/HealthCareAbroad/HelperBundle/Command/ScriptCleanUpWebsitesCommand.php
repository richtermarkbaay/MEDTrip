<?php
/**
* @author Chaztine Blance
*/
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
    /**
* @var OutputInterface
*/
    private $output;

    protected function configure()
    {
        $this
            ->setName('script:loadInstitutionWebsites')
            ->setDescription('Check scripts')
            ->addArgument('file', InputArgument::REQUIRED, 'Absolute path to file that will contain data that was not imported.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $results = $em->getRepository('InstitutionBundle:Institution')->findAll();

        $defaultValue = array();
        $string = array();
        $main = '';
        $facebook = '';
        $twitter = '';
        $googleplus = '';
        $countBroke = 0;
        $count = 0;
        foreach($results as $institution) {
            $oldData = $institution->getWebsiteBackUp();
            $websitesArray = json_decode(\stripslashes($oldData), true);

            if($this->startsWith($oldData, "{")){
                if (\is_array($websitesArray)) {
                    if(isset($websitesArray['main'])) {
                        $this->setData($institution, isset($websitesArray['main']) ? $websitesArray['main'] : '', isset($websitesArray['facebook']) ? $websitesArray['facebook'] : '', isset($websitesArray['twitter']) ? $websitesArray['twitter'] : '', isset($websitesArray['googleplus']) ? $websitesArray['googleplus'] : '');
                        $count ++;
                    }
                }else{
                    $this->output->writeln('id '.$institution->getId());
                    $this->output->writeln('old '.$oldData);
                    $data = explode(',', $oldData);

                      foreach ($data as $key => $currentString) {
                          // remove site type key to get the URI part
                          $string[] = $currentString;
                          \preg_match('/^\{?\"\w+\"\:/', $string[$key], $matches);
                          $uri = \preg_replace('/^\{?\"\w+\"\:/', '', $string[$key]);
                          $uri = $this->stripInvalidChars($uri);

                          if( $this->stripInvalidChars($matches[0]) == 'main'){
                              $main = $uri;
                          }if( $this->stripInvalidChars($matches[0]) == 'facebook'){
                              $facebook = $uri;
                          }
                          if( $this->stripInvalidChars($matches[0]) == 'twitter'){
                              $twitter = $uri;
                          }
                          if( $this->stripInvalidChars($matches[0]) == 'googleplus'){
                              $googleplus = $uri;
                          }

                      }

                      $this->setData($institution,$main, $facebook, $twitter, $googleplus);
                      $countBroke ++;
                      $myContent[] = array( 'id' => $institution->getId(),'Hospital name' => $institution->getName(),'website data' => $oldData);
                }
            }
            $em->persist($institution);
            $this->output->writeln('new '.$institution->getWebsites());
        }
        $em->flush();
        $this->output->writeln('done');
        $this->output->writeln('count website backup:'. $count);
        $this->output->writeln('count broken website json backup:'. $countBroke);

        //$myFile = '/Users/Chaztine/websites/healthcareabroad.com/hca_draft/web/HospitalBrokenSites.txt';
        $myFile = $input->getArgument('file');
        file_put_contents($myFile, print_r($myContent, true));
        exit;
    }

    private function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    private function stripInvalidChars($string)
    {
        $pattern = '/[\\\{\"\:(\s+)]/';
        $s = preg_replace($pattern,'', $string);

        return $s;
    }
    private function setData($institution ,$main, $facebook, $twitter, $googlePlus)
    {
        $institution->setWebsites(isset($main) ? $main : '' );

        if($institution->getSocialMediaSites() == '' || $institution->getSocialMediaSites() == '{"facebook":"","twitter":"","googleplus":""}'){
            $defaultValue['facebook'] = isset($facebook) ? $facebook : '';
            $defaultValue['twitter'] = isset($twitter) ? $twitter : '';
            $defaultValue['googleplus'] = $googlePlus;
            $institution->setSocialMediaSites(\json_encode($defaultValue));

            $this->output->writeln('socialMediaSites '.$institution->getSocialMediaSites());
        }

        return $institution;
    }
}