<?php

namespace HealthCareAbroad\ApiBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Doctrine\ORM\Query;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionApiService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function setDoctrine(Registry $v)
    {
        $this->doctrine = $v;
    }
    
    /**
     * Find an institution by slug
     * 
     * @param string $slug
     * @return array institution data
     */
    public function findBySlug($slug)
    {
        $qb = $this->getQueryBuilderForInstitution();
        $qb->andWhere('inst.slug = :slug')
            ->setParameter('slug', $slug); 

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
    
    public function isSingleCenterInstitutionType($type)
    {
        return InstitutionTypes::SINGLE_CENTER == $type;
    }
    
    /**
     * 
     * @param int $institutionId
     * @return array of doctors hydrated with HYDRATE_ARRAY
     */
    public function getAllDoctors($institutionId)
    {
        $qb = $this->doctrine->getRepository('DoctorBundle:Doctor')->getAllDoctorsByInstitution($institutionId);
        
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
    
    /**
     * Get active instituion specializations of an instituion
     * 
     * @param int $institutionId
     * @return array of institution specializations hydrated with HYDRATE_ARRAY
     */
    public function getActiveInstitutionSpecializations($institutionId)
    {
        $specializations = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')
            ->getActiveSpecializationsByInstitution($institutionId, Query::HYDRATE_ARRAY);
        
        return $specializations;
    }
    
    /**
     * Get a flat array of specializations of an institution
     * 
     * @param int $institutionId
     */
    public function listActiveSpecializations($institutionId)
    {
        $institutionSpecializations = $this->getActiveInstitutionSpecializations($institutionId);
        
        $list = array();
        foreach ($institutionSpecializations as $_each) {
            $specialization = $_each['specialization'];
            $list[$specialization['id']] = $specialization['name'];
        }
        
        return $list;
    }
    
    /**
     * 
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getQueryBuilderForInstitution()
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $qb->select('inst, imc, ct, co, st, icd, fm, lg, gal')
            ->from('InstitutionBundle:Institution', 'inst')
            ->innerJoin('inst.institutionMedicalCenters', 'imc')
            ->leftJoin('inst.city', 'ct')
            ->leftJoin('inst.country', 'co')
            ->leftJoin('inst.state', 'st')
            ->leftJoin('inst.contactDetails', 'icd')
            ->leftJoin('inst.featuredMedia', 'fm')
            ->leftJoin('inst.logo', 'lg')
            ->leftJoin('inst.gallery', 'gal')
            ->where('1=1')
            ->andWhere('inst.status = :activeStatus')
                ->setParameter('activeStatus', InstitutionStatus::getBitValueForApprovedStatus());
        
        return $qb;
    }
}