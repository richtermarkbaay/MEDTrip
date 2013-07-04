<?php

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CleanUpContactDetailsCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;
    
    /**
     * @var Registry
     */
    private $doctrine;
    
    protected function configure()
    {
        $this->setName('helper:cleanUpContactDetails')->setDescription('Cleanup invalid data in contact details');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->doctrine = $this->getContainer()->get('doctrine');
        
        // clean up institution contact details
        $this->output->writeln("============== CLEANING UP INSTITUTION ==============");
        $this->cleanUpInstitutionContactDetails();
        $this->output->writeln("=====================================================");
        
        $this->output->writeln("================ CLEANING UP CLINIC =================");
        $this->cleanUpClinicContactDetails();
        $this->output->writeln("=====================================================");
        
        $this->output->writeln("================ CLEANING UP DOCTOR =================");
        $this->cleanUpDoctorContactDetails();
        $this->output->writeln("=====================================================");
        
        $this->output->writeln("================ CLEANING UP USER =================");
        $this->cleanUpUserContactDetails();
        $this->output->writeln("=====================================================");
    }
    
    private function cleanUpInstitutionContactDetails()
    {
        $institutions = $this->doctrine->getRepository('InstitutionBundle:Institution')->findAll();
        
        $em = $this->doctrine->getEntityManager();
        foreach ($institutions as $institution) {
            $this->output->write("Clean up contact details of institution #{$institution->getId()} [");
            foreach ($institution->getContactDetails() as $contactDetail) {
                
                $this->output->write('.');
                $this->doCleanUp($contactDetail, $institution->getContactNumber(), $institution->getCountry());
                
                
                
                $em->persist($contactDetail);
            }
            $this->output->writeln('] Done.');
        }
        
        $em->flush();
    }
    
    private function cleanUpClinicContactDetails()
    {
        $clinics = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findAll();
        $em = $this->doctrine->getEntityManager();
        foreach ($clinics as $imc) {
            $this->output->write("Clean up contact details of clinic #{$imc->getId()} [");
            foreach ($imc->getContactDetails() as $contactDetail) {
                $this->output->write('.');
                $this->doCleanUp($contactDetail, $imc->getContactNumber(), $imc->getInstitution()->getCountry());
                $em->persist($contactDetail);
            }
            $this->output->writeln('] Done.');
        }
        $em->flush();
    }
    
    private function cleanUpDoctorContactDetails()
    {
        $doctors = $this->doctrine->getRepository('DoctorBundle:Doctor')->findAll();
        $em = $this->doctrine->getEntityManager();
        foreach ($doctors as $doc){
            $countContactDetails = $doc->getContactDetails()->count();
            if (0 == $countContactDetails) {
                // no data
                continue;
            }
            $country = $doc->getCountry();
            $this->output->write("Clean up contact details of doctor #{$doc->getId()} [");
            foreach ($doc->getContactDetails() as $contactDetail) {
                $number = $contactDetail->getNumber();
                if (!is_numeric($number)) {
                    $number = \preg_replace('/\D/', '', $number);
                }
                
                $number = (int) $number;
                if (!$number || strlen($number) < 5) {
                    $contactDetail->setIsInvalid(true);
                }
                $contactDetail->setNumber($number);
                
                // this is from new widget
                if (!$doc->getContactNumber()) {
                    $contactDetail->setFromNewWidget(true);
                }
                $em->persist($contactDetail);
                $this->output->write('.');
                if (!$contactDetail->getIsInvalid() && strlen($number) < 5) {
                    var_dump($contactDetail); exit;
                }
            }
            
            
            $this->output->writeln('] Done.');
        }
        
        $em->flush();
    }
    
    private function cleanUpUserContactDetails()
    {
        $em = $this->doctrine->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('a, b')
            ->from('UserBundle:InstitutionUser', 'a')
            ->innerJoin('a.contactDetails', 'b');
        $users = $qb->getQuery()->getResult();
        foreach ($users as $user) {
            $this->output->write('Cleaning up for user #'.$user->getId(). ' [');
            foreach ($user->getContactDetails() as $contactDetail){
                
                $number = $contactDetail->getNumber();
                if (!\is_numeric($number)) {
                    $number = \preg_replace('/\D/', '', $number);
                }
                $contactDetail->setNumber((int)$number);
                if (!$number || strlen($number) < 5) {
                    $contactDetail->setIsInvalid(true);
                }
                $contactDetail->setFromNewWidget(true);
                $em->persist($contactDetail);
                $this->output->write('.');
            }
            $this->output->writeln('] Done.');
        }
        $em->flush();
    }
    
    private function doCleanUp(ContactDetail $contactDetail, $oldContactNumber = null, Country $country=null)
    {
        if ($oldContactNumber) {
            $oldContactDetail = $contactDetail->__toString();
            $parts = \json_decode($oldContactNumber, true);
            if ($parts) {
                $countryCode = isset($parts['country_code']) ? $parts['country_code'] : null;
                $areaCode = isset($parts['area_code']) ? $parts['area_code'] : null;
                $contactDetail->setCountryCode($countryCode);
                $contactDetail->setAreaCode($areaCode);
                $number = $parts['number'];
            }
            
        }
        // no old contact number
        else {
            // this data must have been entered through the new widget, we need them to verify this data
            $contactDetail->setFromNewWidget(true);
            $number = $contactDetail->getNumber();
        }
        
        if (!\is_numeric($number)) {
            $number = \preg_replace('/\D/', '', $number);
        }
        
        $number = (int)$number;
        $contactDetail->setNumber($number);
        if (!$number || strlen($number) < 5) {
            $contactDetail->setIsInvalid(true);
        }
        
        
    }
}