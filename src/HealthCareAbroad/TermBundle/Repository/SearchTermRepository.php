<?php

namespace HealthCareAbroad\TermBundle\Repository;

use HealthCareAbroad\TermBundle\Entity\SearchTerm;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use Doctrine\ORM\EntityRepository;

class SearchTermRepository extends EntityRepository
{
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
        ->leftJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->leftJoin('inst.gallery', 'ga')
        ->where('a.documentId = :documentId')
        ->andWhere('a.type = :documentType')
        ->andWhere('a.status = :searchTermActiveStatus' )
        ->setParameters($params);
        
        return $qb;
    }
}