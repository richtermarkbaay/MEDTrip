<?php

namespace HealthCareAbroad\TermBundle\Services;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

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
    
    static public function getValidDocumentTypes()
    {
        return array(
            TermDocument::TYPE_SPECIALIZATION => 'specialization',
            TermDocument::TYPE_SUBSPECIALIZATION => 'sub_specialization',
            TermDocument::TYPE_TREATMENT => 'treatment',
        );
    }
    
    /**
     * Helper function to create document object
     * NB: maybe this does not fit here...
     * 
     * @param int $documentId
     * @param int $type
     */
    public function createDocumentObject(TermDocument $termDocument)
    {
        return $this->createDocumentObjectFromDocumentIdAndType($termDocument->getDocumentId(), $termDocument->getType());
    }
    
    public function createDocumentObjectFromDocumentIdAndType($documentId, $documentType)
    {
        $returnObj = null;
        switch ($documentType)
        {
            case TermDocument::TYPE_SPECIALIZATION:
                $returnObj = $this->doctrine->getRepository('TreatmentBundle:Specialization')->find($documentId);
                break;
            case TermDocument::TYPE_SUBSPECIALIZATION:
                $returnObj = $this->doctrine->getRepository('TreatmentBundle:SubSpecialization')->find($documentId);
                break;
            case TermDocument::TYPE_TREATMENT:
                $returnObj = $this->doctrine->getRepository('TreatmentBundle:Treatment')->find($documentId);
                break;
        }
        
        return $returnObj;
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
    
    /**
     * 
     * @param int $documentId
     * @param int $type
     * @param boolean $excludeTermFromDocumentName Do not include the term that is automatically taken from the document object name
     */
    public function findByDocumentIdAndType($documentId, $type, $excludeTermFromDocumentName=true)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('a')
            ->from('TermBundle:Term', 'a')
            ->innerJoin('a.termDocuments', 'b')
            ->where('b.documentId = :documentId')
            ->andWhere('b.type = :documentType')
            ->setParameter('documentId', $documentId)
            ->setParameter('documentType', $type);
        // do not include the term that is automatically taken from the document object name
        if ($excludeTermFromDocumentName) {
            $documentObject = $this->createDocumentObjectFromDocumentIdAndType($documentId, $type);
            if ($documentObject) {
                $qb->andWhere('a.name != :documentObjectName')
                    ->setParameter('documentObjectName', $documentObject->getName());
            }
        }
        
        return $qb->getQuery()->getResult();
        
        
    }
    
    public function saveTreatmentTerms(Treatment $treatment, array $termIds=array())
    {
        $repo = $this->doctrine->getRepository('TermBundle:Term');
        $currentTerm = $repo->findOneByName($treatment->getName());
        // delete current term documents except for the one that is pointing to the name of this specialization
        $this->_deleteTermDocumentsExceptForCurrentTerm($currentTerm, $treatment->getId(), TermDocument::TYPE_TREATMENT);
        
        if (empty($termIds)) {
        
            return false;
        }
        
        // add the termIds to this document
        $this->doctrine->getRepository('TermBundle:TermDocument')->saveBulkTerms($termIds, $treatment->getId(), TermDocument::TYPE_TREATMENT);
        
        return true;
    }
    
    private function _deleteTermDocumentsExceptForCurrentTerm(Term $currentTerm, $documentId, $type)
    {
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->delete('TermBundle:TermDocument', 'a')
            ->where('a.documentId = :documentId')
            ->setParameter('documentId', $documentId)
            ->andWhere('a.type = :type')
            ->setParameter('type', $type);
        
        // if there is a matched term by name
        if ($currentTerm) {
            $qb->andWhere('a.term != :currentTermId')
            ->setParameter('currentTermId', $currentTerm->getId());
        }
        
        $qb->getQuery()->execute();
        
    }
    
}