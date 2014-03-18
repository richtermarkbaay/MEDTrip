<?php

namespace HealthCareAbroad\TermBundle\Repository;

use Doctrine\ORM\Query;

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

    public function findAllActiveTermsGroupedBySpecialization()
    {
        $parameters = array('treatmentType' => TermDocument::TYPE_TREATMENT, 'activeSearchTermStatus' => SearchTerm::STATUS_ACTIVE);
        // get the treatment_ids that are available in search terms
        $sql = "SELECT a.documentId FROM TermBundle:SearchTerm a
        WHERE a.type = :treatmentType AND a.status = :activeSearchTermStatus
        GROUP BY a.documentId, a.type";

        $query = $this->getEntityManager()->createQuery($sql)
            ->setParameters($parameters);

        // find a way to flatten this result without looping
        $result = $query->getArrayResult();
        $treatmentIds = array();
        foreach ($result as $row) {
            $treatmentIds[] = $row['documentId'];
        }

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('sp, tr, sub_sp')
            ->from('TreatmentBundle:Specialization', 'sp')
            ->innerJoin('sp.treatments', 'tr')
            ->leftJoin('tr.subSpecializations', 'sub_sp')
            ->where($qb->expr()->in('tr.id', ':treatmentIds'))
            ->andWhere('sp.status = :specializationStatus')
            ->andWhere('tr.status = :treatmentStatus')

            ->orderBy('sp.name, tr.name')
            ->setParameter('treatmentIds', $treatmentIds)
            ->setParameter('specializationStatus', Specialization::STATUS_ACTIVE)
            ->setParameter('treatmentStatus', Treatment::STATUS_ACTIVE);

        return $qb->getQuery()->getResult();
    }

    public function findActiveCountriesWithCities()
    {
        $status = SearchTerm::STATUS_ACTIVE;

        $connection = $this->getEntityManager()->getConnection();

        $sql = "
            SELECT b.id AS country_id, b.name AS country_name, b.slug AS country_slug, c.name AS city_name, c.slug AS city_slug
            FROM search_terms AS a
            LEFT JOIN countries AS b ON a.country_id = b.id
            LEFT JOIN cities AS c ON a.city_id = c.id
            WHERE a.status = {$status}
            GROUP BY country_name, city_name ORDER BY country_name, city_name ASC";

        $stmt = $connection->query($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

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
    public function getQueryBuilderByDocumentIdAndType($documentId, $documentType, $groupedByCenters = true)
    {
        $params = array(
            'documentId' => $documentId,
            'documentType' => $documentType,
            'searchTermActiveStatus' => SearchTerm::STATUS_ACTIVE
        );
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('a, imc, inst, co, ci, imcLogo')
        ->from('TermBundle:SearchTerm', 'a')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->leftJoin('imc.logo', 'imcLogo')
        ->innerJoin('imc.institution', 'inst')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->where('a.documentId = :documentId')
        ->andWhere('a.type = :documentType')
        ->andWhere('a.status = :searchTermActiveStatus' )
        ->setParameters($params);

        if ($groupedByCenters) {
            $qb->groupBy('imc.id');
        }

        // order by clinic ranking points
        $qb->orderBy('imc.rankingPoints', 'DESC');
        //$qb->orderBy('inst.totalClinicRankingPoints', 'DESC');

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
        $qb->select('a, imc, inst, co, ci, instLogo')
        ->from('TermBundle:SearchTerm', 'a')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->innerJoin('a.institution', 'inst')
        ->leftJoin('inst.logo', 'instLogo')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
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

        // order by totalRankingPoints
        $qb->orderBy('inst.totalClinicRankingPoints', 'DESC');

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
        $qb->select('a, imc, inst, co, ci, imcLogo, instLogo')
        ->from('TermBundle:SearchTerm', 'a')
        ->leftJoin('a.term', 'term')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->leftJoin('imc.logo', 'imcLogo')
        ->innerJoin('a.institution', 'inst')
        ->leftJoin('inst.logo', 'instLogo')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->where('a.term = :termId')
        ->andWhere('a.status = :searchTermActiveStatus')
        ->setParameter('termId', $termId)
        ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);

        // order by clinic ranking points
        $qb->orderBy('imc.rankingPoints', 'DESC');

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

    public function findByTerms(array $termIds = array(), array $filters = array())
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a, imc, inst, co, ci, imcLogo, instLogo')
        ->from('TermBundle:SearchTerm', 'a')
        ->innerJoin('a.institutionMedicalCenter', 'imc')
        ->leftJoin('imc.logo', 'imcLogo')
        ->innerJoin('a.institution', 'inst')
        ->leftJoin('inst.logo', 'instLogo')
        ->innerJoin('inst.country', 'co')
        ->leftJoin('inst.city', 'ci')
        ->leftJoin('a.term', 't')
        ->where('a.status = :searchTermActiveStatus')
        ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);

        $hasNoTermIds = false;
        if (empty($termIds)) {
            $hasNoTermIds = true;
        } else {
            $qb->andWhere($qb->expr()->in('t.id', $termIds));
        }

        foreach ($filters as $filter => $value) {
            if ($filter == 'treatmentName' && $hasNoTermIds) {
                $qb->andWhere('t.name LIKE :treatmentName');
                $qb->setParameter('treatmentName', '%'.$value.'%');
            }

            if ($filter == 'destinationName') {
                $qb->andWhere('(co.name LIKE :destinationName OR ci.name LIKE :destinationName)');
                //$qb->andWhere('ci.name LIKE :destinationName');
                $qb->setParameter('destinationName', '%'.$value.'%');
            }

            if ($filter == 'countryId') {
                $qb->andWhere('co.id = :countryId');
                $qb->setParameter('countryId', $value, \PDO::PARAM_INT);
            }

            if ($filter == 'cityId') {
                $qb->andWhere('ci.id IS NOT NULL');
                $qb->andWhere('ci.id = :cityId');
                $qb->setParameter('cityId', $value, \PDO::PARAM_INT);
            }
        }

        $qb->groupBy('imc.id');

        // order by clinic ranking points
        $qb->orderBy('imc.rankingPoints', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findByFilters(array $filters = array()) {
        return $this->getQueryBuilderFilteredBy($filters)->getQuery()->getResult();
    }

    private function getQueryBuilderFilteredBy(array $filters = array()) {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('a, imc, inst, co, ci')
            ->from('TermBundle:SearchTerm', 'a')
            ->innerJoin('a.institutionMedicalCenter', 'imc')
            ->innerJoin('a.institution', 'inst')
            ->innerJoin('inst.country', 'co')
            ->leftJoin('inst.city', 'ci')
            ->where('a.status = :searchTermActiveStatus')
            ->setParameter('searchTermActiveStatus', SearchTerm::STATUS_ACTIVE);

        $clinicSearchResults = false;
        foreach ($filters as $filter) {
            switch (get_class($filter)) {
                case 'HealthCareAbroad\TreatmentBundle\Entity\Specialization':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_SPECIALIZATION);
                    $clinicSearchResults = true;
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_SUBSPECIALIZATION);
                    $clinicSearchResults = true;
                    break;

                case 'HealthCareAbroad\TreatmentBundle\Entity\Treatment':
                    $qb->andWhere('a.documentId = :documentId')
                    ->andWhere('a.type = :type')
                    ->setParameter('documentId', $filter->getId())
                    ->setParameter('type', TermDocument::TYPE_TREATMENT);
                    $clinicSearchResults = true;
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

        if ($clinicSearchResults) {
            // order by clinic ranking points
            $qb->orderBy('imc.rankingPoints', 'DESC');
        }
        else {
            // order by instititution total ranking points
            $qb->orderBy('inst.totalClinicRankingPoints', 'DESC');
        }
        
        //$qb->orderBy('inst.totalClinicRankingPoints', 'DESC');

        return $qb;
    }
}