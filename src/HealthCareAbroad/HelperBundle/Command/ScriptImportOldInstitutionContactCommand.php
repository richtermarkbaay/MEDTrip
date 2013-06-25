<?php
/**
 * @author Alnie Jacobe
*/

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;
use Doctrine\Tests\ORM\Proxy\SleepClass;
use Assetic\Exception\Exception;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptImportOldInstitutionContactCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadInstitutionContacts')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        //get all institution contacts        
        $contacts = array();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $institutionResult = $em->getRepository('InstitutionBundle:Institution')->findAll();
        
        //get contactDetails of each institution
        
        $contactDetail = new ContactDetail();
        foreach($institutionResult as $institution) {
            $institution = $this->saveContactDetail($institution);
            echo "institutionId: ".$institution->getId()."\n";
            echo \memory_get_usage(true)."\n";
            $em->persist($institution);
        }
        $em->flush();
        
        echo "done\n";
        exit;
    
    }
    public function saveContactDetail(Institution $institution)
    {
        $contactIdsArray = array();
        $contactNumber = $institution->getContactNumber();
        $contactNumber =  \json_decode($institution->getContactNumber(), true);
        if (\is_array($contactNumber)){
            if($contactNumber) {
                $contactDetail = new ContactDetail();
                $contactDetail->setNumber($contactNumber['country_code']);
                $contactDetail->setAreaCode($contactNumber['area_code']);
                $contactDetail->setNumber($contactNumber['number']);
                $contactDetail->setType(ContactDetailTypes::PHONE);
                $institution->addContactDetail($contactDetail);
            }
        }
        
        return $institution;
    }
}