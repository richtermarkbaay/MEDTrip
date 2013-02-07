<?php

namespace HealthCareAbroad\TermBundle\Repository;

use HealthCareAbroad\HelperBundle\Entity\City;

use HealthCareAbroad\HelperBundle\Entity\Country;

use HealthCareAbroad\TermBundle\Entity\SearchTerm;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\EntityRepository;

class SearchTermRepository extends EntityRepository
{
    public function findByCity(City $city)
    {
        $qb = $this->getQueryBuilderByDestination($city->getCountry(), $city);
        
        return $qb->getQuery()->getResult();
    }
    
    public function findByCountry(Country $country)
    {
        $qb = $this->getQueryBuilderByDestination($country);
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Find active search terms by specialization
     * 
     * @param Specialization $specialization
     */
    public function findBySpecialization(Specialization $specialization)
    {
        $qb = $this->getQueryBuilderByDocumentIdAndType($specialization->getId(), TermDocument::TYPE_SPECIALIZATION);
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Find active search terms by Treatment 
     * 
     * @param Treatment $treatment
     * @return array SearchTerm
     */
    public function findByTreatment(Treatment $treatment)
    {
        $qb = $this->getQueryBuilderByDocumentIdAndType($treatment->getId(), TermDocument::TYPE_TREATMENT);
        
        // TODO: integrate pager here or not?
        
        return $qb->getQuery()->getResult();
    }
    
    /**
     * Get Query builder for active search terms
     * 
     * @param int $documentId
     * @param int $documentType
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByDocumentIdAndType($documentId, $documentType)
    {
        $params = array(
            'documentId' => $documentId, 
            'documentType' => $documentType,
            'searchTermActiveStatus' => SearchTerm::STATUS_ACTIVE
        );
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a, imc, inst, co, ci, ga')
        ->from('TermBundle:SearchTerm', 'a')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->innerJoin('imc.institution', 'inst')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->leftJoin('inst.gallery', 'ga')
        ->where('a.documentId = :documentId')
        ->andWhere('a.type = :documentType')
        ->andWhere('a.status = :searchTermActiveStatus' )
        ->setParameters($params);
        
        return $qb;
    }
    
    /**
     * Get query builder by destination
     * We may not be able to mix query with getQueryBuilderByDocumentIdAndType since this will be grouped by institution and not by clinic
     * @param Country $country
     * @param City $city
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByDestination(Country $country, City $city = null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a, imc, inst, co, ci, ga')
        ->from('TermBundle:SearchTerm', 'a')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->innerJoin('a.institution', 'inst')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->leftJoin('inst.gallery', 'ga')
        ->where('co.id = :countryId')
        ->andWhere('a.status = :searchTermActiveStatus')
        ->setParameter('countryId', $country->getId())
        ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);
        
        if (!\is_null($city)) {
            $qb->andWhere('ci.id = :cityId')
            ->setParameter('cityId', $city->getId());
        }
        
        // we may not need this?
        $qb->groupBy('inst.id');
        
        return $qb;
    }
}