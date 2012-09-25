<?php
namespace HealthCareAbroad\SearchBundle\Services;

use Doctrine\ORM\Query\ResultSetMapping;

use HealthCareAbroad\SearchBundle\Constants;
use Doctrine\ORM\EntityManager;

/**
 * Temporary holder of all search related functionality
 *
 */
class SearchService
{
    private $entityManager;
    private $repositoryMap = array(
        Constants::SEARCH_CATEGORY_INSTITUTION => 'InstitutionBundle:Institution',
        Constants::SEARCH_CATEGORY_CENTER => 'MedicalProcedureBundle:MedicalCenter',
        Constants::SEARCH_CATEGORY_PROCEDURE_TYPE => 'MedicalProcedureBundle:MedicalProcedureType',
        Constants::SEARCH_CATEGORY_PROCEDURE => 'MedicalProcedureBundle:MedicalProcedure'
    );

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     *
     * @param array $searchCriteria
     * @todo rename method
     */
    public function initiate(array $searchCriteria = array())
    {
        $repository = $this->entityManager->getRepository($this->repositoryMap[$searchCriteria['category']]);

        return $repository->search($searchCriteria['term']);
    }

    /**
     * Retrieves treatments (procedures or procedure types) by name with optional
     * destination filter.
     *
     * @param string $name
     * @param mixed $destinationId
     * @return string
     */
    public function getJsonEncodedTreatmentsByName($name, $destinationId = 0)
    {
        $result = array();

        if (empty($destinationId)) {
            $procedures = $this->searchTreatmentsByName($name);
        } else {
            $procedures = $this->searchTreatmentsByNameWithDestination($name, $destinationId);
        }

        foreach ($procedures as $p ) {
            // use medical procedure type name if medical procedure name is empty
            $label = $p['medical_procedure_name'] ? $p['medical_procedure_name'] : $p['medical_procedure_type_name'];
            $value = $p['medical_procedure_type_id'].'-'.$p['medical_procedure_id'];

            $result[] = array('label' => $label, 'value' => $value);
        }

        return \json_encode($result);
    }

    public function getJsonEncodedDestinationsByName($name, $treatmentId = 0)
    {
        $result = array();

        if (empty($treatmentId)) {
            $destinations = $this->searchDestinationsByName($name);
        } else {
            $destinations = $this->searchDestinationsByNameWithTreatment($name, $treatmentId);
        }

        foreach ($destinations as $d ) {
            //concatenate city and country names if they are both present
            $label = $d['city_name'] ? $d['city_name'].', '.$d['country_name'] : $d['country_name'];
            $value = $d['country_id'].'-'.$d['city_id'];

            $result[] = array('label' => $label, 'value' => $value);
        }

        return \json_encode($result);
    }

    /**
     * Searches treatments (procedures and procedure types) by name.
     *
     * @param string $name
     */
    public function searchTreatmentsByName($name)
    {
        $connection = $this->entityManager->getConnection();

        $sql ="
            SELECT a.id AS medical_procedure_id, b.name AS medical_procedure_name, c.id AS medical_procedure_type_id, c.name AS medical_procedure_type_name
            FROM institution_medical_procedures AS a
            LEFT JOIN medical_procedures AS b ON a.medical_procedure_id = b.id
            LEFT JOIN medical_procedure_types AS c ON b.medical_procedure_type_id = c.id
            WHERE a.status = 1
            AND (b.name LIKE :name OR c.name LIKE :name)

            UNION

            SELECT 0, '', a.id, b.name
            FROM institution_medical_procedure_types AS a
            LEFT JOIN medical_procedure_types AS b ON a.medical_procedure_type_id = b.id
            WHERE a.status = 1 AND b.name LIKE :name

            ORDER BY medical_procedure_type_name ASC, medical_procedure_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$name.'%');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Search treatments (medical procedures or medical procedure types) located
     * at destinations with $destinationId.
     *
     * @param string $treatmentTerm
     * @param int $destionationId
     * @todo optimize sql; verify that results are what we want;
     *       compare query performance with the one using subselects in
     *       searchDestinationsByNameWithTreatementId
     */
    public function searchTreatmentsByNameWithDestination($treatmentTerm, $destinationId)
    {
        $connection = $this->entityManager->getConnection();

        $destinationIds = $this->parseIds($destinationId, 'destination');
/*
        $sql = "
        SELECT a.id, b.name
        FROM institution_medical_procedures AS a
        LEFT JOIN medical_procedures AS b ON a.medical_procedure_id = b.id
        LEFT JOIN institution_medical_procedure_types AS c ON a.institution_medical_procedure_type_id = c.id
        LEFT JOIN institution_medical_centers AS d ON c.institution_medical_center_id = d.id
        LEFT JOIN institutions AS e ON d.institution_id = e.id
        WHERE b.name LIKE :procedureTerm
        AND e.country_id = :countryId
        ";

        if ($destination['cityId']) {
            $sql .= ' AND e.id = :cityId ';
        }
*/
        $sql ="
            SELECT a.id AS medical_procedure_id, b.name AS medical_procedure_name, c.id AS medical_procedure_type_id, c.name AS medical_procedure_type_name
            FROM institution_medical_procedures AS a
            LEFT JOIN medical_procedures AS b ON a.medical_procedure_id = b.id
            LEFT JOIN medical_procedure_types AS c ON b.medical_procedure_type_id = c.id


            LEFT JOIN institution_medical_procedure_types AS d ON a.institution_medical_procedure_type_id = d.id
            LEFT JOIN institution_medical_centers AS e ON d.institution_medical_center_id = e.id
            LEFT JOIN institutions AS f ON e.institution_id = f.id
            WHERE (b.name LIKE :treatmentTerm OR c.name LIKE :treatmentTerm)
            AND f.country_id = :countryId
        ";

        if ($destinationIds['cityId']) {
            $sql .= "
            AND f.id = :cityId
            ";
        }

        $sql .= "
            UNION

            SELECT 0, '', a.id, b.name
            FROM institution_medical_procedure_types AS a
            LEFT JOIN medical_procedure_types AS b ON a.medical_procedure_type_id = b.id
            LEFT JOIN institution_medical_centers AS c ON a.institution_medical_center_id = a.id
            LEFT JOIN institutions AS d ON c.institution_id = d.id
            WHERE a.status = 1 AND b.name LIKE :treatmentTerm
            AND d.country_id = :countryId
        ";

        if ($destinationIds['cityId']) {
            $sql .= "
            AND f.id = :cityId
            ";
        }

        $sql .= "
            ORDER BY medical_procedure_type_name ASC, medical_procedure_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('treatmentTerm', '%'.$treatmentTerm.'%');
        $stmt->bindValue('countryId', $destinationIds['countryId']);

        if ($destinationIds['cityId']) {
            $stmt->bindValue('cityId', $destinationIds['cityId']);
        }

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function searchDestinationsByName($name)
    {
        $connection = $this->entityManager->getConnection();

        $sql ="
        SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
        FROM cities AS a
        LEFT JOIN countries AS b ON a.country_id = b.id
        WHERE a.status = 1 AND b.status = 1
        AND (a.name LIKE :name OR b.name LIKE :name)

        UNION

        SELECT 0, '', id, name FROM countries WHERE status = 1 AND name LIKE :name

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
     * @param string $name
     * @param int $treatmentId
     * @todo optimize sql; verify that results are what we want
     * 	     compare query performance with the one used in
     * 		 searchTreatmentByNameWithDestination()
     */
    public function searchDestinationsByNameWithTreatment($destinationTerm, $treatmentId)
    {
        $connection = $this->entityManager->getConnection();

        $treatmentIds = $this->parseIds($treatmentId, 'treatment');

        // The subqueries are non-correlated but mysql executes them as dependent
        // subqueries. Used EXISTS to force mysql optimizer to run them only once.
        // This bug is fixed (?) in later versions (mysql 6.*)
        $sql ="
            SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
            FROM cities AS a
            LEFT JOIN countries AS b ON a.country_id = b.id
            WHERE a.status = 1 AND b.status = 1
            AND (a.name LIKE :destinationTerm OR b.name LIKE :destinationTerm)
            AND (
                EXISTS (
                SELECT a.id FROM cities AS a
                LEFT JOIN institutions AS b ON a.id = b.city_id
                LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
                LEFT JOIN institution_medical_procedure_types AS d ON c.id = d.institution_medical_center_id
                LEFT JOIN institution_medical_procedures AS e ON d.id = e.institution_medical_procedure_type_id
                WHERE e.id = :treatmentId)

                OR

                EXISTS (
                SELECT a.id FROM countries AS a
                LEFT JOIN institutions AS b ON a.id = b.country_id
                LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
                LEFT JOIN institution_medical_procedure_types AS d ON c.id = d.institution_medical_center_id
                LEFT JOIN institution_medical_procedures AS e ON d.id = e.institution_medical_procedure_type_id
                WHERE e.id = :treatmentId)
            )

            UNION

            SELECT 0, '', id, name
            FROM countries
            WHERE status = 1 AND name LIKE :destinationTerm
            AND EXISTS (
                SELECT a.id FROM countries AS a
                LEFT JOIN institutions AS b ON a.id = b.country_id
                LEFT JOIN institution_medical_centers AS c ON b.id = c.institution_id
                LEFT JOIN institution_medical_procedure_types AS d ON c.id = d.institution_medical_center_id
                LEFT JOIN institution_medical_procedures AS e ON d.id = e.institution_medical_procedure_type_id
                WHERE e.id = :treatmentId)

            ORDER BY country_name ASC, city_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('destinationTerm', '%'.$destinationTerm.'%');
        $stmt->bindValue('treatmentId', $treatmentId);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function parseIds($ids, $context)
    {
        list($majorId, $minorId) = \explode('-', $ids);

        if ('treatment' == $context) {
            $majorLabel = 'medicalProcedureTypeId';
            $minorLabel = 'medicalProcedureId';
        } else if ('destination' == $context){
            $majorLabel = 'countryId';
            $minorLabel = 'cityId';
        }

        return array($majorLabel => (int) $majorId, $minorLabel => (int) $minorId);
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