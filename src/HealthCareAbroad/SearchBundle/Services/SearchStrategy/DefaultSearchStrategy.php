<?php
namespace HealthCareAbroad\SearchBundle\Services\SearchStrategy;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy;

/**
 * DefaultSearchStrategy
 *
 * @author Harold Modesto <harold.modesto@chromedia.com>
 *
 */
class DefaultSearchStrategy extends SearchStrategy
{
    /**
     * Search
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * @return array $array
     *
     * @see \HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy::search()
     *
     */
    public function search(SearchParameterBag $searchParams)
    {
        $this->isViewReadyResults = true;
        $this->entityManager = $this->container->get('doctrine')->getEntityManager();

        if ($searchParams->has('term') && $searchParams->get('term')) {
            $this->searchAutoComplete($searchParams);
        }

        return $this->results;
    }

    //TODO: refactor
    private function searchAutoComplete(SearchParameterBag $searchParams)
    {
        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $results = $this->getDestinationsByName($searchParams);
                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $results = $this->getTreatmentsByName($searchParams);
                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:
                if ($searchParams->get('term') === $searchParams->get('treatmentLabel')) {
                    $results = $this->getTreatmentsByName($searchParams);
                } elseif ($searchParams->get('term') === $searchParams->get('destinationLabel')) {
                    $results = $this->getDestinationsByName($searchParams);
                }

                break;

            default:
                throw new \Exception('Unknown context: '. $searchParams->get('context'));
        }

        $this->results = $results;
    }

    /**
     * Retrieves treatments (specialization, subspecialization, treatment) by
     * name with optional destination filter.
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * @return array $result
     */
    private function getTreatmentsByName(SearchParameterBag $searchParams)
    {
        $result = array();

        if ($searchParams->get('countryId') || $searchParams->get('cityId')) {
            $treatments = $this->searchTreatmentsByNameWithDestination($searchParams);
        } else {
            $treatments = $this->searchTreatmentsByName($searchParams->get('term'));
        }

        foreach ($treatments as $t) {
            $value = (is_null($t['specialization_id']) ? 0 : $t['specialization_id']) .'-'.
                            (is_null($t['sub_specialization_id']) ? 0 : $t['sub_specialization_id']) .'-'.
                            (is_null($t['treatment_id']) ? 0 : $t['treatment_id']) .'-'.
                            $t['treatment_type'];

            $result[] = array('label' => $t['treatment_name'], 'value' => $value);
        }

        return $result;
    }

    private function getDestinationsByName(SearchParameterBag $searchParams)
    {
        $result = array();

        $destinations = null;
        if ($searchParams->get('treatmentType')) {
            $destinations = $this->searchDestinationsByNameWithTreatment($searchParams);
        } else {
            $destinations = $this->searchDestinationsByName($searchParams->get('term'));
        }

        foreach ($destinations as $d) {
            //concatenate city and country names if they are both present
            $label = $d['city_name'] ? $d['city_name'].', '.$d['country_name'] : $d['country_name'];
            $value = $d['country_id'].'-'.$d['city_id'];

            $result[] = array('label' => $label, 'value' => $value);
        }

        return $result;
    }

    /**
     * Searches treatments (specializations, subspecializations and treatment) by name.
     *
     * @param string $name Name to search for
     *
     * @return array
     */
    private function searchTreatmentsByName($name)
    {
        $connection = $this->entityManager->getConnection();

        //TODO: some of the SELECT can still be optimized (e.g. unneeded left joins)
        $sql ="
            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            WHERE a.status = 1
            AND c.name LIKE :name

            UNION

            SELECT
                        f.id AS specialization_id,
                        0 AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            WHERE a.status = 1 AND d.sub_specialization_id IS NULL
            AND c.name LIKE :name

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        0 AS treatment_id,
                        e.name AS treatment_name,
                        'subSpecialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            WHERE a.status = 1
            AND e.name LIKE :name

            UNION

            SELECT
                        f.id AS specialization_id,
                        0 AS sub_specialization_id,
                        0 AS treatment_id,
                        f.name AS treatment_name,
                        'specialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            WHERE a.status = 1
            AND f.name LIKE :name

            GROUP BY treatment_name
            ORDER BY treatment_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$name.'%');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search treatments (specialization, subspecialization or treatments) located
     * at destinations with $destinationId.
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * @return array:
     *
     * @todo optimize sql; verify that results are what we want;
     *       compare query performance with the one using subselects in
     *       searchDestinationsByNameWithTreatementId
     */
    private function searchTreatmentsByNameWithDestination(SearchParameterBag $searchParams)
    {
        $connection = $this->entityManager->getConnection();
        $cityId = $searchParams->get('cityId', 0);
        $countryId = $searchParams->get('countryId', 0);

        $extendedDestinationWhereClause = ' ';
        if ($cityId) {
            $extendedDestinationWhereClause .= " AND h.city_id = :cityId ";
        } else if ($countryId) {
            $extendedDestinationWhereClause .= " AND h.country_id = :countryId ";
        }

        $sql ="
            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON g.institution_id = h.id
            WHERE a.status = 1
            AND c.name LIKE :name
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        0 AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON g.institution_id = h.id
            WHERE a.status = 1 AND d.sub_specialization_id IS NULL
            AND c.name LIKE :name
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        0 AS treatment_id,
                        e.name AS treatment_name,
                        'subSpecialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON g.institution_id = h.id
            WHERE a.status = 1
            AND e.name LIKE :name
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        0 AS sub_specialization_id,
                        0 AS treatment_id,
                        f.name AS treatment_name,
                        'specialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON g.institution_id = h.id
            WHERE a.status = 1
            AND f.name LIKE :name
            $extendedDestinationWhereClause

            GROUP BY treatment_name
            ORDER BY treatment_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$searchParams->get('term').'%');

        if ($cityId) {
            $stmt->bindValue('cityId', $cityId);
        } else if ($countryId) {
            $stmt->bindValue('countryId', $countryId);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function searchDestinationsByName($name)
    {
        $connection = $this->entityManager->getConnection();

        $sql ="
        SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
        FROM cities AS a
        LEFT JOIN countries AS b ON a.country_id = b.id
        LEFT JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
        LEFT JOIN institution_medical_centers AS d ON c.id = d.institution_id
        RIGHT JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
        WHERE a.status = 1 AND b.status = 1
        AND (a.name LIKE :name OR b.name LIKE :name)

        UNION

        SELECT 0, '', a.id, a.name FROM countries AS a
        LEFT JOIN institutions AS b ON a.id = b.country_id
        LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
        RIGHT JOIN institution_specializations AS d ON c.id = d.institution_medical_center_id

        WHERE a.status = 1 AND a.name LIKE :name

        ORDER BY city_name ASC, country_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$name.'%');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Searches destinations (city/country) by $destinationTerm where the treatment
     * with $treatmentId is available.
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * @todo optimize sql; verify that results are what we want
     * 	     compare query performance with the one used in
     * 		 searchTreatmentByNameWithDestination()
     *
     */
    private function searchDestinationsByNameWithTreatment(SearchParameterBag $searchParams)
    {
        $connection = $this->entityManager->getConnection();

        $variableWhereClause = '';
        switch ($searchParams->get('treatmentType')) {
            case 'specialization':
                $variableWhereClause = ' AND h.id=' . $searchParams->get('specializationId');
                break;
            case 'subSpecialization':
                $variableWhereClause = ' AND g.sub_specialization_id=' . $searchParams->get('subSpecializationId');
                break;
            case 'treatment':
                $variableWhereClause = ' AND f.treatment_id=' . $searchParams->get('treatmentId');
                break;
        }

        $sql = "
        SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
        FROM cities AS a
        LEFT JOIN countries AS b ON a.country_id = b.id
        LEFT JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
        LEFT JOIN institution_medical_centers AS d ON c.id = d.institution_id
        LEFT JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
        LEFT JOIN institution_treatments AS f ON e.id = f.institution_specialization_id
        LEFT JOIN treatment_sub_specializations AS g ON f.treatment_id = g.treatment_id
        LEFT JOIN specializations AS h ON e.specialization_id = h.id
        WHERE a.status = 1 AND b.status = 1
        AND (a.name LIKE :name OR b.name LIKE :name)
        $variableWhereClause

        UNION

        SELECT 0, '', a.id, a.name FROM countries AS a
        LEFT JOIN institutions AS b ON a.id = b.country_id
        LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
        LEFT JOIN institution_specializations AS e ON c.id = e.institution_medical_center_id
        LEFT JOIN institution_treatments AS f ON e.id = f.institution_specialization_id
        LEFT JOIN treatment_sub_specializations AS g ON f.treatment_id = g.treatment_id
        LEFT JOIN specializations AS h ON e.specialization_id = h.id
        WHERE a.status = 1 AND a.name LIKE :name
        $variableWhereClause

        ORDER BY city_name ASC, country_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$searchParams->get('term').'%');
        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * getArrayResults
     *
     * @see \HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy::getArrayResults()
     */
    public function getArrayResults()
    {
        $elasticaResults 	= $this->results->getResults();
        $totalResults 		= $this->results->getTotalHits();

        $arrayResults = array();
        foreach ($elasticaResults as $elasticaResult) {
            $arrayResult[] = $elasticaResult->getData();
        }

        return $arrayResults;
    }

    /**
     * getObjectResults
     *
     * @see \HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy::getObjectResults()
     *
     * return array $objecResults
     *
     */
    public function getObjectResults()
    {
        $elasticaResults 	= $this->results->getResults();
        $totalResults 		= $this->results->getTotalHits();

        $objectResults = array();
        foreach ($elasticaResults as $elasticaResult) {
            $objectResult[] = $elasticaResult->getData();
        }

        return $objectResults;
    }

    /**
     * Determines whether or not the strategy is available at runtime
     *
     * (non-PHPdoc)
     * @see \HealthCareAbroad\SearchBundle\Services\SearchStrategy\SearchStrategy::isAvailable()
     *
     */
    public function isAvailable()
    {
        return true;
    }
}