<?php
/**
 * Service class for Institution
 * 
 * @author Allejo Chris G. Velarde
 * @author Alnie Jacobe
 */
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\UserBundle\Services\InstitutionUserService;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionUserInvitation;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\City;
use HealthCareAbroad\HelperBundle\Entity\Country;
class InstitutionService
{
	
    protected $doctrine;
    
    /**
     * @var HealthCareAbroad\UserBundle\Services\InstitutionUserService
     */
    protected $institutionUserService;
    
    public function __construct(\Doctrine\Bundle\DoctrineBundle\Registry $doctrine )
    {
    	$this->doctrine = $doctrine;
    }
    
    /**
     * 
     * @param Institution $institution
     * @return boolean
     */
    public function isActive(Institution $institution)
    {
        $activeStatus = InstitutionStatus::getBitValueForActiveStatus();

        
        return $activeStatus == $activeStatus & $institution->getStatus() ? true : false;
    }
    
    public function setInstitutionUserService(InstitutionUserService $institutionUserService)
    {
        $this->institutionUserService = $institutionUserService;
    }    
    
    public function getAllStaffOfInstitution(Institution $institution)
    {
        $users = $this->doctrine->getRepository('UserBundle:InstitutionUser')->findByInstitution($institution);
        
        $returnValue = array();
        foreach($users as $user) {
            $returnValue[] = $this->institutionUserService->getAccountData($user);
        }
        return $returnValue;
    }
    
    public function getAllActiveMedicalCenters()
    {
        
    }
    
    public function getTreatmentQueryBuilderByInstitution($institution)
    {
        $qry = "SELECT treatment_id FROM institution_treatments WHERE treatment_id = :treatmentId";
        $param = array('treatmentId' => $treatmentId);
        $treatmentIds = $this->_em->getConnection()->executeQuery($qry, $param)->fetchAll();

        var_dump($treatmentIds);
        
        return $count;
    }

    public function getRecentlyAddedMedicalCenters(Institution $institution, QueryOptionBag $options=null)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('i')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'i')
            ->where('i.institution = :institutionId')
            ->orderBy('i.dateCreated','desc')
            ->setParameter('institutionId', $institution->getId());
        
        if ($limit = $options->get(QueryOption::LIMIT, null)) {
            $qb->setMaxResults($limit);
        }
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get the first added medical center of an institution
     * 
     * @param Institution $institution
     * @return InstitutionMedicalCenter
     */
    public function getFirstMedicalCenter(Institution $institution)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('i')
            ->from('InstitutionBundle:InstitutionMedicalCenter', 'i')
            ->where('i.institution = :institutionId')
            ->orderBy('i.dateCreated','asc')
            ->setParameter('institutionId', $institution->getId())
            ->setMaxResults(1);
        
        return $qb->getQuery()->getOneOrNullResult(); 
    }
    
}