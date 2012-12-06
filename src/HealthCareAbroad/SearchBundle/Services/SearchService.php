<?php
namespace HealthCareAbroad\SearchBundle\Services;

use HealthCareAbroad\SearchBundle\SearchParameterBag;

use Symfony\Component\DependencyInjection\Parameter;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

/**
 * Temporary holder of all search related functionality
 *
 */
class SearchService
{
    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function searchByCountry($countryId)
    {
        if (!$country = $this->entityManager->getRepository('HelperBundle:Country')->find($countryId)) {
            throw new \Exception('Country not found');
        }
        $searchResults = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCountry($country);

        return array($searchResults, $country);
    }

    public function searchByCity($cityId, $hydrateCountry = false)
    {
        if ($hydrateCountry) {
            //TODO:
        }

        if (!$city = $this->entityManager->getRepository('HelperBundle:City')->find($cityId)) {
            throw new \Exception('City not found');
        }
        $searchResults = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByCity($city);

        return array($searchResults, $city);
    }

    public function searchBySpecialization($specialization)
    {
        if (is_numeric($specialization)) {
            $specialization = $this->entityManager->getRepository('TreatmentBundle:Specialization')->find($specialization);
        } else if (is_string($specialization)) {
            $specialization = $this->entityManager->getRepository('TreatmentBundle:Specialization')->findOneBy(array('slug' => $specialization));
        }

        if (!$specialization) {
            throw new \Exception('Specialization not found');
        }

        $searchResults = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecialization($specialization);

        return array($searchResults, $specialization);
    }

    public function searchByTreatment($treatment)
    {
        if (is_numeric($treatment)) {
            $treatment = $this->entityManager->getRepository('TreatmentBundle:Treatment')->find($treatment);
        } else if (is_string($treatment)) {
            $treatment = $this->entityManager->getRepository('TreatmentBundle:Treatment')->findOneBy(array('slug' => $treatment));
        }

        if (!$treatment) {
            throw new \Exception('Specialization not found');
        }

        $searchResults = $this->entityManager->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatment($treatment);

        return array($searchResults, $treatment);
    }

    /**
     *
     * @param SearchParameterBag $searchParams
     * @return multitype:Ambigous <NULL, object> Ambigous <NULL, object, string>
     * @todo Rename function
     */
    public function getSearchVariables(SearchParameterBag $searchParams)
    {
        $specialization = null;
        $subSpecialization = null;
        $treatment = null;
        $country = null;
        $city = null;

        if ($treatmentType = $searchParams->get('treatmentType', '')) {
            $specialization = $this->entityManager->getRepository('TreatmentBundle:Specialization')->find($searchParams->get('specializationId'));

            switch ($treatmentType) {
                case 'subSpecialization':
                    $subSpecialization = $this->entityManager->getRepository('TreatmentBundle:SubSpecialization')->find($searchParams->get('subSpecializationId'));
                    break;
                case 'treatment':
                    $treatment = $this->entityManager->getRepository('TreatmentBundle:Treatment')->find($searchParams->get('treatmentId'));
                    break;
            }
        }

        if ($searchParams->get('countryId', 0)) {
            $country = $this->entityManager->getRepository('HelperBundle:Country')->find($searchParams->get('countryId'));
        }

        if ($searchParams->get('cityId', 0)) {
            $city = $this->entityManager->getRepository('HelperBundle:City')->find($searchParams->get('cityId'));
            $country = $city->getCountry();
        }

        return array($specialization, $subSpecialization, $treatment, $country, $city);
    }

    /**
     * Retrieves treatments (specialization, subspecialization, treatment) by
     * name with optional destination filter.
     *
     * @param SearchParameterBag $searchParams
     * @return array $result
     */
    public function getTreatmentsByName(SearchParameterBag $searchParams)
    {
        $result = array();

        if ($searchParams->get('countryId') || $searchParams->get('cityId')) {
            $treatments = $this->searchTreatmentsByNameWithDestination($searchParams);
        } else {
            $treatments = $this->searchTreatmentsByName($searchParams->get('term'));
        }

        foreach ($treatments as $t ) {

            $value = (is_null($t['specialization_id']) ? 0 : $t['specialization_id']) .'-'.
                     (is_null($t['sub_specialization_id']) ? 0 : $t['sub_specialization_id']) .'-'.
                     (is_null($t['treatment_id']) ? 0 : $t['treatment_id']) .'-'.
                     $t['treatment_type'];

            $result[] = array('label' => $t['treatment_name'], 'value' => $value);
        }

        return $result;
    }

    public function getDestinationsByName(SearchParameterBag $searchParams)
    {
        $result = array();

        $destinations = null;
        if ($searchParams->get('treatmentType')) {
            $destinations = $this->searchDestinationsByNameWithTreatment($searchParams);
        } else {
            $destinations = $this->searchDestinationsByName($searchParams->get('term'));
        }

        foreach ($destinations as $d ) {
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
     * @param string $name
     */
    private function searchTreatmentsByName($name)
    {
        $connection = $this->entityManager->getConnection();

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
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
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
     * @param SearchParameterBag $searchParams
     * @return array:
     * @todo optimize sql; verify that results are what we want;
     *       compare query performance with the one using subselects in
     *       searchDestinationsByNameWithTreatementId
     */
    private function searchTreatmentsByNameWithDestination(SearchParameterBag $searchParams)
    {
        $connection = $this->entityManager->getConnection();
        $cityId = $searchParams->get('cityId', 0);
        $countryId = $searchParams->get('countryId', 0);

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
            AND c.name LIKE :name ";

            if ($cityId) {
                $sql .= " AND h.city_id = :cityId ";
            } else if ($countryId) {
                $sql .= " AND h.country_id = :countryId ";
            }

        $sql .="

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
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
            AND e.name LIKE :name ";

            if ($cityId) {
                $sql .= " AND h.city_id = :cityId ";
            } else if ($countryId) {
                $sql .= " AND h.country_id = :countryId ";
            }

        $sql .="

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
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
            AND f.name LIKE :name ";

            if ($cityId) {
                $sql .= " AND h.city_id = :cityId ";
            } else if ($countryId) {
                $sql .= " AND h.country_id = :countryId ";
            }

        $sql .="
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
     * @param string $name
     * @param int $treatmentId
     * @todo optimize sql; verify that results are what we want
     * 	     compare query performance with the one used in
     * 		 searchTreatmentByNameWithDestination()
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

    public function getCountriesWithTreatment(Treatment $treatment)
    {
        $sql = "
            SELECT DISTINCT country.id, country.name
            FROM institution_treatments AS a
            LEFT JOIN institution_medical_centers AS b ON a.institution_medical_center_id = b.id
            LEFT JOIN institutions AS c ON b.institution_id = c.id
            LEFT JOIN countries AS country ON c.country_id = country.id
            LEFT JOIN treatments AS d ON a.treatment_id = d.id
            WHERE d.id = :treatmentId
        ";

        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->bindValue('treatmentId', $treatment->getId());
        $stmt->execute();

        return $stmt->fetchAll();
    }

}