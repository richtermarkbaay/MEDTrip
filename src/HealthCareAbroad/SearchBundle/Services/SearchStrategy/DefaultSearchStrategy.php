<?php
namespace HealthCareAbroad\SearchBundle\Services\SearchStrategy;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Doctrine\DBAL\Connection;

use Doctrine\DBAL\Statement;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\TreatmentBundle\Entity\SubSpecialization;

use HealthCareAbroad\TreatmentBundle\Entity\Treatment;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

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
     * TODO: this should be renamed ***autocomplete***?
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

        switch ($searchParams->get('context')) {
            case SearchParameterBag::SEARCH_TYPE_DESTINATIONS:
                $results = $this->getDestinationsByName($searchParams);

                break;

            case SearchParameterBag::SEARCH_TYPE_TREATMENTS:
                $results = $this->getTreatmentsByName($searchParams);

                break;

            case SearchParameterBag::SEARCH_TYPE_COMBINATION:
                if ($searchParams->get('searchedTerm') === $searchParams->get('treatmentLabel')) {
                    $results = $this->getTreatmentsByName($searchParams);
                } elseif ($searchParams->get('searchedTerm') === $searchParams->get('destinationLabel')) {
                    $results = $this->getDestinationsByName($searchParams);
                }

                break;

            default:
                throw new \Exception('Unknown context: '. $searchParams->get('context'));
        }

        return $results;
    }

    public function getTermDocuments(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $sql ="
            SELECT a.*
            FROM term_frontend_documents AS a
            INNER JOIN terms AS b ON b.id = a.term_id
            WHERE b.id = :termId
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('termId', $searchParams->get('treatmentId'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Retrieves treatments (specialization, subspecialization, treatment) by
     * name with optional destination filter.
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * @return array $result
     *
     * @todo Use TermFrontendDocument entity
     */
    private function getTreatmentsByName(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $optionalWhereClause = ' ';
        if ($cityId = $searchParams->get('cityId', 0)) {
            $optionalWhereClause .= " AND b.city_id = :cityId ";
        } elseif ($countryId = $searchParams->get('countryId', 0)) {
            $optionalWhereClause .= " AND b.country_id = :countryId ";
        }

        $sql = "
            SELECT a.id AS value, a.name AS label
            FROM terms AS a
            INNER JOIN term_frontend_documents AS b ON a.id = b.term_id
            WHERE a.name LIKE :name
            $optionalWhereClause
            GROUP BY a.name
            ORDER BY a.name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$searchParams->get('searchedTerm').'%');
        if ($cityId) {
            $stmt->bindValue('cityId', $cityId);
        } elseif ($countryId) {
            $stmt->bindValue('countryId', $countryId);
        }
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getDestinationsByName(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $optionalWhereClause = ($termId = $searchParams->get('treatmentId', 0)) ? ' AND a.id = :treatmentId ' : ' ';

        //TODO: test if cast really helps speed up query?
        $sql = "
            SELECT CONCAT(c.name, ', ', d.name) AS label, CONCAT(CAST(d.id AS CHAR), '-', CAST(c.id AS CHAR)) AS value
            FROM terms AS a
            INNER JOIN term_frontend_documents AS b ON b.term_id = a.id
            INNER JOIN cities AS c ON c.id = b.city_id
            LEFT JOIN countries AS d ON d.id = c.country_id
            WHERE c.name LIKE :name OR d.name LIKE :name
            $optionalWhereClause

            UNION

            SELECT d.name AS label, CONCAT(CAST(d.id AS CHAR), '-0') AS value
            FROM terms AS a
            INNER JOIN term_frontend_documents AS b ON b.term_id = a.id
            LEFT JOIN countries AS d ON d.id = b.country_id
            WHERE a.name LIKE :name
            $optionalWhereClause

            GROUP BY label
            ORDER BY label ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$searchParams->get('searchedTerm').'%');
        if ($termId) {
            $stmt->bindValue('treatmentId', $termId);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function searchCountriesByNameWithTreatment(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $treatmentId = null;
        $name = '"%'.$searchParams->get('searchedTerm').'%"';
        $institutionStatus = InstitutionStatus::getBitValueForActiveAndApprovedStatus();

        switch ($searchParams->get('treatmentType')) {
            case 'specialization':
                $treatmentId = $searchParams->get('specializationId');

                //TODO: This is virtually the same query used for searchDestinationsByNameWithTreatment minus
                //the union on cities but will returns less results if institution status is set
                // AND c.status = $institutionStatus
                $sql = "
                    SELECT 0 AS city_id, '' AS city_name, a.id AS country_id, a.name AS country_name
                    FROM countries AS a
                    LEFT JOIN institutions AS b ON a.id = b.country_id
                    LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
                    LEFT JOIN institution_specializations AS e ON c.id = e.institution_medical_center_id
                    WHERE a.name LIKE $name

                    AND e.specialization_id = $treatmentId
                    GROUP BY country_id
                    ORDER BY country_name ASC
                ";

                break;

            case 'subSpecialization':
                $treatmentId = $searchParams->get('subSpecializationId');
                $sql = "
                    SELECT 0 AS city_id, '' AS city_name, a.id AS country_id, a.name AS country_name
                    FROM countries AS b
                    LEFT JOIN institutions AS c ON b.id = c.country_id
                    LEFT JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    LEFT JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    LEFT JOIN institution_treatments AS f ON f.specialization_id = e.id
                    LEFT JOIN treatments AS g ON f.treatment_id = g.id
                    LEFT JOIN treatment_sub_specializations AS h ON g.id = h.treatment_id
                    WHERE b.name LIKE $name
                    AND c.status = $institutionStatus
                    AND h.sub_specialization_id = $treatmentId
                    GROUP BY country_id
                    ORDER BY city_name ASC, country_name ASC
                ";
                break;

            case 'treatment':
                $treatmentId = $searchParams->get('treatmentId');
                $sql = "
                    SELECT 0 AS city_id, '' AS city_name, a.id AS country_id, a.name AS country_name
                    FROM countries AS b
                    LEFT JOIN institutions AS c ON b.id = c.country_id
                    LEFT JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    LEFT JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    LEFT JOIN institution_treatments AS f ON f.specialization_id = e.id
                    WHERE b.name LIKE $name
                    AND c.status = $institutionStatus
                    AND f.treatment_id = $treatmentId
                    GROUP BY country_id
                    ORDER BY city_name ASC, country_name ASC
                ";
                break;
        }

        $stmt = $connection->executeQuery($sql);
        //TODO: use prepared statements. there seems to be a bug though: the code
        //below will return an empty result
//         $stmt = $connection->prepare($sql);
//         $stmt->bindValue('name', '"%'.$searchParams->get('term').'%"');
//         $stmt->bindValue('treatmentId', $treatmentId, \PDO::PARAM_INT);
        //$stmt->bindValue('institutionStatus', InstitutionStatus::INACTIVE, \PDO::PARAM_INT);

        return $stmt->fetchAll();
    }

    private function searchCitiesByNameWithTreatment(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $treatmentId = null;
        $name = '"%'.$searchParams->get('searchedTerm').'%"';
        $institutionStatus = InstitutionStatus::getBitValueForActiveAndApprovedStatus();
        $imcStatus = InstitutionMedicalCenterStatus::APPROVED;

        switch ($searchParams->get('treatmentType')) {
            case 'specialization':
                $treatmentId = $searchParams->get('specializationId');

                $sql = "
                    SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
                    FROM cities AS a
                    LEFT JOIN countries AS b ON a.country_id = b.id
                    INNER JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    WHERE a.name LIKE $name
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND e.specialization_id = $treatmentId
                ";

                break;

            case 'subSpecialization':
                $treatmentId = $searchParams->get('subSpecializationId');
                $sql = "
                    SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
                    FROM cities AS a
                    LEFT JOIN countries AS b ON a.country_id = b.id
                    INNER JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    INNER JOIN institution_treatments AS f ON f.specialization_id = e.id
                    LEFT JOIN treatments AS g ON f.treatment_id = g.id
                    INNER JOIN treatment_sub_specializations AS h ON g.id = h.treatment_id
                    WHERE a.name LIKE $name
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND h.sub_specialization_id = $treatmentId
                ";
                break;

            case 'treatment':
                $treatmentId = $searchParams->get('treatmentId');
                $sql = "
                    SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
                    FROM cities AS a
                    LEFT JOIN countries AS b ON a.country_id = b.id
                    INNER JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    INNER JOIN institution_treatments AS f ON f.specialization_id = e.id
                    WHERE a.name LIKE $name
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND f.treatment_id = $treatmentId
                ";
                break;
        }

        $stmt = $connection->executeQuery($sql);
            //TODO: use prepared statements. there seems to be a bug though: the code
            //below will return an empty result
    //         $stmt = $connection->prepare($sql);
    //         $stmt->bindValue('name', '"%'.$searchParams->get('term').'%"');
    //         $stmt->bindValue('treatmentId', $treatmentId, \PDO::PARAM_INT);
            //$stmt->bindValue('institutionStatus', InstitutionStatus::INACTIVE, \PDO::PARAM_INT);

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

    /**
     * Replaces any parameter placeholders in a query with the value of that
     * parameter. Useful for debugging. Assumes anonymous parameters from
     * $params are are in the same order as specified in $query
     *
     * Usage:
     * var_dump(interpolateQuery($sql, array('destinationTerm' => '%'.$destinationTerm.'%', 'treatmentId' => $treatmentId))); exit;
     *
     * @param string $query The sql query with parameter placeholders
     * @param array $params The array of substitution parameters
     * @return string The interpolated query
     */
    private function interpolateQuery($query, $params) {

        $keys = array();
        $values = $params;

        # build a regular expression for each parameter
        foreach ($params as $key => $value) {
            if (is_string($key)) {
                $keys[] = '/:'.$key.'/';
            } else {
                $keys[] = '/[?]/';
            }

            if (is_array($value))
                $values[$key] = implode(',', $value);

            if (is_null($value))
                $values[$key] = 'NULL';
        }
        // Walk the array to see if we can add single-quotes to strings
        array_walk($values, create_function('&$v, $k', 'if (!is_numeric($v) && $v!="NULL") $v = "\'".$v."\'";'));

        $query = preg_replace($keys, $values, $query, 1, $count);

        return $query;
    }
}