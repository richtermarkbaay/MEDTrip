<?php
/**
 * @author Alnie Jacobe
*/

namespace HealthCareAbroad\HelperBundle\Command;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterProperty;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionProperty;

use Doctrine\Tests\ORM\Proxy\SleepClass;
use Assetic\Exception\Exception;
use HealthCareAbroad\HelperBundle\Entity\CommandScriptLog;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ScriptImportInstitutionPropertiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('script:importInstitutionProperties')->setDescription('Check scripts');
    }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $result = $em->getRepository('InstitutionBundle:InstitutionProperty')->findAll();
        
        $subQuery = '';
        $valuesCriteria = array();
        foreach($result as $insProperty) {
            //get all imc first
            echo 'id => '.$insProperty->getId();
            $centers = $insProperty->getInstitution()->getInstitutionMedicalCenters();
            foreach($centers as $center) {
                //check if already exists
                $ifExist = $this->checkIfExists($insProperty, $center);
                if(!$ifExist) {
                    $valuesCriteria[] = "(". $insProperty->getInstitution()->getId()  .", ". $center->getId() .", ". $insProperty->getInstitutionPropertyType()->getId() .", '". $insProperty->getValue()."', '". $insProperty->getExtraValue() ."' )";
                    
                    echo 'imcId => '.$center->getId()."\n";
                    echo \memory_get_usage(true)."\n";
                }
            }
        }
        
        $subQuery = implode(', ', $valuesCriteria);
        $sqlQuery = "INSERT INTO institution_medical_center_properties (institution_id, institution_medical_center_id, institution_property_type_id, value, extra_value) VALUES $subQuery";
        $conn = $em->getConnection();
        $conn->executeQuery($sqlQuery);
        $conn->close();

        echo "done\n";
        exit;
    }
    
    public function checkIfExists(InstitutionProperty $institutionProperty, InstitutionMedicalCenter $center)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
        ->select('a')
        ->from('InstitutionBundle:InstitutionMedicalCenterProperty', 'a')
        ->where('a.institution = :institution')
        ->andWhere('a.institutionMedicalCenter = :imc')
        ->andWhere('a.institutionPropertyType = :insProperty')
        ->andWhere('a.value = :value')
        ->setParameter('institution', $institutionProperty->getInstitution()->getId())
        ->setParameter('imc', $center->getId())
        ->setParameter('insProperty', $institutionProperty->getInstitutionPropertyType()->getId())
        ->setParameter('value', $institutionProperty->getValue())
        ->getQuery();

        if(count($qb->getResult()) >= 1 ) {
            return true;
        }
        
        return false;
        
    }
}