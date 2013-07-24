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
//         $this->output->writeln("============== CLEANING UP INSTITUTION ==============");
//         $this->cleanUpInstitutionContactDetails();
//         $this->output->writeln("=====================================================");
        
//         $this->output->writeln("================ CLEANING UP CLINIC =================");
//         $this->cleanUpClinicContactDetails();
//         $this->output->writeln("=====================================================");
        
//         $this->output->writeln("================ CLEANING UP DOCTOR =================");
//         $this->cleanUpDoctorContactDetails();
//         $this->output->writeln("=====================================================");
        
//         $this->output->writeln("================ CLEANING UP USER =================");
//         $this->cleanUpUserContactDetails();
//         $this->output->writeln("=====================================================");

        // populate country_id field based on country_code
        $this->output->writeln("================ POPULATING COUNTRY FIELD =================");
        $this->populateCountryField();
        $this->output->writeln("=====================================================");
    }
    
    private function populateCountryField()
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('cd')
            ->from('HelperBundle:ContactDetail', 'cd')
            ->where('cd.number IS NOT NULL')
            ->andWhere('cd.countryCode IS NOT NULL')
            ->andWhere('cd.country IS NULL');
        $contactDetails = $qb->getQuery()->getResult();
        $countriesByCountryCode = array();
        $em = $this->doctrine->getManager();
        foreach ($contactDetails as $contactDetail) {
            $this->output->write("id:[#{$contactDetail->getId()}] -> {$contactDetail->__toString()} [");
            $code = (int)$contactDetail->getCountryCode();
            
            if (!$code){
                $this->output->writeln("No CC]");
                continue;
            }
            
            if (!isset($countriesByCountryCode[$code])) {
                $countries = $this->doctrine->getRepository('HelperBundle:Country')->findByCode($code);
                $countriesByCountryCode[$code] = $countries;
            }
            
            if (count($countriesByCountryCode[$code]) == 1){
                // we only set those countries that have unique country codes
                $contactDetail->setCountry($countriesByCountryCode[$code][0]);
                $em->persist($contactDetail);
                $this->output->writeln("OK]");
            }
            else {
                $this->output->writeln("Multiple CC]");
            }
        }
        
        $this->output->writeln("Flushing to db");
        $em->flush();
        
        // populate remaining contact details by cross matching the owner objects of the contact detail
        // -- start with institution contact details
        $em = $this->doctrine->getManager();
        $this->output->writeln("=====================================================");
        $this->output->writeln("Populate remaining data by cross matching institution contact details");
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('inst, cd, co')
            ->from('InstitutionBundle:Institution', 'inst')
            ->innerJoin('inst.contactDetails', 'cd')
            ->innerJoin('inst.country', 'co')
            ->where('cd.country IS NULL')
            ->andWhere('cd.number IS NOT NULL')
            ->andWhere('cd.countryCode IS NOT NULL');
        $objects = $qb->getQuery()->getResult();
        foreach ($objects as $obj) {
            $this->output->writeln("id: #{$obj->getId()} >> ");
            if ($country = $obj->getCountry()){
                $this->_updateCountryFieldOfContactDetailsByCountry($country, $obj->getContactDetails(), $em);
            }
        }
        $em->flush();
        $this->output->writeln("=====================================================");
        // -- end with institution contact details
        
        // -- start with doctor contact details
        $em = $this->doctrine->getManager();
        $this->output->writeln("=====================================================");
        $this->output->writeln("Populate remaining data by cross matching doctor contact details");
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('doc, cd, co')
        ->from('DoctorBundle:Doctor', 'doc')
        ->innerJoin('doc.contactDetails', 'cd')
        ->innerJoin('doc.country', 'co')
        ->where('cd.country IS NULL')
        ->andWhere('cd.number IS NOT NULL')
        ->andWhere('cd.countryCode IS NOT NULL');
        $objects = $qb->getQuery()->getResult();
        foreach ($objects as $obj) {
            $this->output->writeln("id: #{$obj->getId()} >> ");
            if ($country = $obj->getCountry()){
                $this->_updateCountryFieldOfContactDetailsByCountry($country, $obj->getContactDetails(), $em);
            }
        }
        $em->flush();
        // -- end with doctor contact details
        
        // -- start with clinic contact details
        $em = $this->doctrine->getManager();
        $this->output->writeln("=====================================================");
        $this->output->writeln("Populate remaining data by cross matching doctor contact details");
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('imc, cd, co, inst')
        ->from('InstitutionBundle:InstitutionMedicalCenter', 'imc')
        ->innerJoin('imc.institution', 'inst')
        ->innerJoin('imc.contactDetails', 'cd')
        ->innerJoin('inst.country', 'co')
        ->where('cd.country IS NULL')
        ->andWhere('cd.number IS NOT NULL')
        ->andWhere('cd.countryCode IS NOT NULL');
        $objects = $qb->getQuery()->getResult();
        foreach ($objects as $obj) {
            $this->output->writeln("id: #{$obj->getId()} >> ");
            if ($country = $obj->getInstitution()->getCountry()){
                $this->_updateCountryFieldOfContactDetailsByCountry($country, $obj->getContactDetails(), $em);
            }
        }
        $em->flush();
        // -- end with clinic contact details
        
    }
    
    private function _updateCountryFieldOfContactDetailsByCountry(Country $country, $contactDetails = array(), $em)
    {
        foreach ($contactDetails as $contactDetail) {
            $this->output->write("    #{$contactDetail->getId()} -> {$contactDetail->__toString()} [");
            if (!$contactDetail->getCountry()){
                $code = (int)$contactDetail->getCountryCode();
                if ($code && $code == $country->getCode()){
                    $contactDetail->setCountry($country);
                    $em->persist($contactDetail);
                    $this->output->writeln("OK]");
                }
                else {
                    
                    $this->output->writeln("Invalid CC {$code} == {$country->getCode()}] C.id={$country->getId()}");
                }
            }
        }
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