<?php
/**
 * @author Alnie Jacobe
*/

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;
use Doctrine\Tests\ORM\Proxy\SleepClass;
use Assetic\Exception\Exception;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptImportOldContactCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:loadContacts')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        //get all doctors contacts        
        $contacts = array();
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $doctorsResult = $em->getRepository('DoctorBundle:Doctor')->findAll();
        
        //get contactDetails of each doctor
        $contactDetail = new ContactDetail();
        foreach($doctorsResult as $doctor) {
            $doctor = $this->saveContactDetail($doctor);
            echo "doctorId: ".$doctor->getId()."\n";
            echo \memory_get_usage(true)."\n";
            $em->persist($doctor);
        }
        $em->flush();
        
        echo "done\n";
        exit;
    
    }
    public function saveContactDetail(Doctor $doctor)
    {
        $contactIdsArray = array();
        $contactNumberArray =  \json_decode($doctor->getContactNumber(), true);
        if (\is_array($contactNumberArray)){
            foreach($contactNumberArray as $each){
                if($each['number']) {
                    $type = $this->getContactDetailType($each['type']);
                    $contactDetail = new ContactDetail();
                    $contactDetail->setNumber($each['number']);
                    $contactDetail->setType($this->getContactDetailType($each['type']));
                    $doctor->addContactDetail($contactDetail);
                }
            }    
        }
        
        return $doctor;
    }
    
    public function saveEntity($entity) 
    {
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $entity;
    }
    
    
    public function getContactDetailType($contactType)
    {
        if($contactType == 'phone') {
            $contactType = ContactDetail::TYPE_PHONE;
        }
        else if($contactType == 'mobile') {
            $contactType = ContactDetail::TYPE_MOBILE;
        }
        else {
            $contactType = ContactDetail::TYPE_FAX;
        }
        
        return $contactType;
            
    }
}