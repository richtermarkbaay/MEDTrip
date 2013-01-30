<?php

namespace HealthCareAbroad\TermBundle\Services;

use HealthCareAbroad\TermBundle\Entity\Term;

use HealthCareAbroad\TermBundle\Entity\TermDocument;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\HelperBundle\Classes\QueryOption;

use HealthCareAbroad\HelperBundle\Classes\QueryOptionBag;

use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for Terms.
 * Service id: services.terms
 * 
 * @author Allejo Chris G. Velarde
 */
class TermsService
{
    /**
     * @var Registry
     */
    private $doctrine;
    
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    
    public function findByName($name, $limit=null)
    {
        $queryOptions = new QueryOptionBag();
        if ($limit) {
            $queryOptions->add(QueryOption::LIMIT, $limit);
        }
        
        return $this->doctrine->getRepository('TermBundle:Term')
            ->findByName($name, $queryOptions);
    }
    
    public function saveSpecializationTerms(Specialization $specialization, array $termIds=array())
    {
        $currentTerm = $this->doctrine->getRepository('TermBundle:Term')->findOneByName($specialization->getName());
        
        // delete current term documents except for the one that is pointing to the name of this specialization
        $this->_deleteTermDocumentsExceptForCurrentTerm($currentTerm, $specialization->getId(), TermDocument::TYPE_SPECIALIZATION);
        
        // DELETE a FROM TermBundle:TermDocument WHERE
         
    }
    
    private function _deleteTermDocumentsExceptForCurrentTerm(Term $currentTerm, $documentId, $type)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->delete('TreatmentBundle:TermDocument', 'a')
            ->where('a.documentId = :documentId')
            ->setParameter('documentId', $documentId)
            ->andWhere('a.type = :type')
            ->setParameter('type', $type);
        
        // if there is a matched term by name
        if ($currentTerm) {
            $qb->andWhere('a.term != :currentTermId')
            ->setParameter('currentTermId', $currentTerm->getId());
        }
        
        
    }
    
}