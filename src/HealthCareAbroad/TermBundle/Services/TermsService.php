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

    public function removeTreatment(Treatment $treatment)
    {
        $em = $this->doctrine->getEntityManager();
        $em->remove($treatment);
        $em->flush();      

        return true;
        
    }
    
    public function convertTreatmentToTerm($selectedTreatmentId, Treatment $oldTreatment)
    {
        $currentTreatment = $this->doctrine->getRepository('TreatmentBundle:Treatment')->findOneById($selectedTreatmentId);
        
        // get the old treatment term
        $oldTreatmentTerm = $this->getTreatmentInternalTerm($oldTreatment);
        
        // move institution treatments of old treatment to new treatment
        $this->doctrine->getRepository('TermBundle:Term')->moveInstitutionTreatmentsToAnotherTreatment($currentTreatment, $oldTreatment);
        
        // delete the term documents of the old treatment
        $this->deleteTermDocumentsByDocumentIdAndType($oldTreatment->getId(), TermDocument::TYPE_TREATMENT);
        
        // save old treatment term as new term for current treatment
        $this->doctrine->getRepository('TermBundle:TermDocument')->saveBulkTerms(array($oldTreatmentTerm->getId()), $currentTreatment->getId(), TermDocument::TYPE_TREATMENT);
        
        $this->removeTreatment($oldTreatment);
        
        return $currentTreatment->getName();
    }
    
    /**
     * Get the internal Term of a Treatment
     * 
     * @param Treatment $treatment
     * @return Term
     */
    public function getTreatmentInternalTerm(Treatment $treatment)
    {
        $qb = $this->doctrine->getManager()->createQueryBuilder();
        $qb->select('t, td')
            ->from('TermBundle:Term', 't')
            ->innerJoin('t.termDocuments', 'td')
            ->where('td.documentId = :treatmentId')
                ->setParameter('treatmentId', $treatment->getId())
            ->andWhere('td.type = :documentType')
                ->setParameter('documentType', TermDocument::TYPE_TREATMENT)
            ->andWhere('t.name = :treatmentName')
                ->setParameter('treatmentName', $treatment->getName())
            ->andWhere('t.internal = 1');
        
        $term = $qb->getQuery()->getOneOrNullResult();
        
        return $term;
    }
    
    private function deleteTermDocumentsByDocumentIdAndType($documentId, $type)
    {
        $qb = $this->doctrine->getRepository('TermBundle:Term')->getQueryBuilderForDeletingTermDocumentsByDocumentIdAndType($documentId, $type);
        
        $qb->getQuery()->execute();
    }
    
    
    private function _deleteTermDocumentsExceptForCurrentTerm(Term $currentTerm, $documentId, $type)
    {
        $qb = $this->doctrine->getRepository('TermBundle:Term')->getQueryBuilderForDeletingTermDocumentsByDocumentIdAndType($documentId, $type);
            
        $qb->andWhere('a.term != :currentTermId')
            ->setParameter('currentTermId', $currentTerm->getId());
        
        $qb->getQuery()->execute();

    }

    public function getActiveCountriesWithCities()
    {
        $results = $this->doctrine->getRepository('TermBundle:SearchTerm')->findActiveCountriesWithCities();

        $length = count($results);
        if ($length < 1) {
            return array();
        }

        $currentCountryName = '';
        $countries = array();
        $cities = array();

        //Asssumptions: array is sorted by country and city name
        for ($i = 0; $i < $length; $i++) {
            $currentItem = $results[$i];

            if ($currentItem['country_name'] != $currentCountryName) {
                $country = array('name' => $currentItem['country_name'], 'slug' => $currentItem['country_slug']);
            }

            if ($currentItem['city_name']) {
                $cities[] = array('name' => $currentItem['city_name'], 'slug' => $currentItem['city_slug']);
            }

            $nextItem = isset($results[$i + 1]) ? $results[$i + 1] : array();

            if ((isset($nextItem['country_name']) && $currentItem['country_name'] != $nextItem['country_name']) || empty($nextItem)) {
                $country['cities'] = $cities;
                $countries[] = $country;

                $cities = array();
            }
        }

        return $countries;
    }

}