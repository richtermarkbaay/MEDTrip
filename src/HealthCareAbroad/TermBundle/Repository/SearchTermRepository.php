<?php

namespace HealthCareAbroad\TermBundle\Repository;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

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
     * Find active search terms by Term
     *
     * @param mixed Term $term
     * @return array SearchTerm
     */
    public function findByTerm($term)
    {
        $termId = is_object($term) ? $term->getId() : $term;

        $qb = $this->getQueryBuilderByTerm($termId);

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

    /**
     * Get query builder by termId
     *
     * @param integer $termId
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderByTerm($termId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a, imc, inst, co, ci, ga')
        ->from('TermBundle:SearchTerm', 'a')
        ->leftJoin('a.term', 'term')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->innerJoin('a.institution', 'inst')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->leftJoin('inst.gallery', 'ga')
        ->where('a.term = :termId')
        ->andWhere('a.status = :searchTermActiveStatus')
        ->setParameter('termId', $termId)
        ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);

        return $qb;
    }

    public function getTopCountries($limit = 6)
    {
        $connection = $this->getEntityManager()->getConnection();

        $status = SearchTerm::STATUS_ACTIVE;
        //TODO: revisit code; the returned result may not be what we want
        $stmt = $connection->prepare("
            SELECT b.id, b.name, b.slug AS slug, COUNT(a.country_id) AS count
            FROM search_terms a
            LEFT JOIN countries b ON a.country_id = b.id
            WHERE a.status = $status
            GROUP BY b.id
            ORDER BY count DESC
            LIMIT :limit
       ");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getTopTreatments($limit = 6)
    {
        $connection = $this->getEntityManager()->getConnection();

        $status = SearchTerm::STATUS_ACTIVE;
        //TODO: revisit code; the returned result may not be what we want
        $stmt = $connection->prepare("
            SELECT b.id, b.name, b.slug AS slug, COUNT(a.treatment_id) AS count, c.id AS specialization_id, c.name AS specialization_name, c.slug AS specialization_slug
            FROM search_terms a
            INNER JOIN treatments b ON a.treatment_id = b.id
            LEFT JOIN specializations c ON a.specialization_id = c.id
            WHERE a.status = $status
            GROUP BY b.id
            ORDER BY count DESC
            LIMIT :limit
        ");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findBySubSpecialization(SubSpecialization $subSpecialization)
    {
        $qb = $this->getQueryBuilderByDocumentIdAndType($subSpecialization->getId(), TermDocument::TYPE_SUBSPECIALIZATION);

        return $qb->getQuery()->getResult();
    }

//     public function findBySpecializationAndCountry(Specialization $specialization, Country $country)
//     {
//         return $this->getQueryBuilderFilteredBy(array($specialization, $country))->getQuery()->getResult();
//     }

//     public function findBySpecializationAndCity(Specialization $specialization, City $city)
//     {
//         return $this->getQueryBuilderFilteredBy(array($specialization, $city))->getQuery()->getResult();
//     }

//     public function findBySubSpecializationAndCountry(SubSpecialization $subSpecialization, Country $country, Specialization $specialization = null)
//     {
//         $filters = array($subSpecialization, $country);
//         if ($specialization) {
//             $filters[] = $specialization;
//         }

//         return $this->getQueryBuilderFilteredBy($filters)->getQuery()->getResult();
//     }

//     public function findBySubSpecializationAndCity(SubSpecialization $subSpecialization, City $city, Specialization $specialization)
//     {
//         $filters = array($subSpecialization, $country);
//         if ($specialization) {
//             $filters[] = $specialization;
//         }

//         return $this->getQueryBuilderFilteredBy(array($subSpecialization, $city))->getQuery()->getResult();
//     }

//     public function findByTreatmentAndCountry(Treatment $treatment, Country $country)
//     {
//         return $this->getQueryBuilderFilteredBy(array($treatment, $country))->getQuery()->getResult();
//     }

//     public function findByTreatmentAndCity(Treatment $treatment, City $city)
//     {
//         return $this->getQueryBuilderFilteredBy(array($treatent, $city))->getQuery()->getResult();
//     }

    public function findByFilters(array $filters = array()) {
        return $this->getQueryBuilderFilteredBy($filters)->getQuery()->getResult();
    }

    private function getQueryBuilderFilteredBy(array $filters = array()) {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a, imc, inst, co, ci, ga')
            ->from('TermBundle:SearchTerm', 'a')
            ->innerJoin('a.institutionMedicalCenter', 'imc')
            ->innerJoin('a.institution', 'inst')
            ->innerJoin('inst.country', 'co')
            ->leftJoin('inst.city', 'ci')
            ->leftJoin('inst.gallery', 'ga')
            ->where('a.status = :searchTermActiveStatus')
            ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);

        foreach ($filters as $filter) {
            switch (get_class($filter)) {
                case 'HealthCareAbroad\TreatmentBundle\Entity\Specialization':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_SPECIALIZATION);
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_SUBSPECIALIZATION);
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\Treatment':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_TREATMENT);
                    break;

                case 'HealthCareAbroad\HelperBundle\Entity\Country':
                    $qb->andWhere('co.id = :countryId')
                    ->setParameter('countryId', $filter->getId());
                    break;

                case 'HealthCareAbroad\HelperBundle\Entity\City':
                    $qb->andWhere('ci.id = :cityId')
                    ->setParameter('cityId', $filter->getId());
                    break;
                default:
                    throw new \Exception('Unsupported filter');
            }
        }

        // we may not need this?
        $qb->groupBy('imc.id');

        return $qb;
    }
}