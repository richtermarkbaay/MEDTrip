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
                if ($searchParams->get('term') === $searchParams->get('treatmentLabel')) {
                    $results = $this->getTreatmentsByName($searchParams);
                } elseif ($searchParams->get('term') === $searchParams->get('destinationLabel')) {
                    $results = $this->getDestinationsByName($searchParams);
                }

                break;

            default:
                throw new \Exception('Unknown context: '. $searchParams->get('context'));
        }

        return $results;
    }

    //TODO: refactor
    private function searchAutoComplete(SearchParameterBag $searchParams)
    {

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
            $treatments = $this->searchTreatmentsByName($searchParams);
        }

        $prevTreatmentName = -1;
        $prevSubSpecializationName = -1;

        //TODO: this is not working as intended. the specs is if we have similarly
        //named treatment or subspecialization to append the specialization to the
        //label

        //NOTE: this assumes that treatment ids that are equal will be grouped together
        //however, the same assumption for subSpecialization cannot be safely made as of
        //the moment - the query will still need to be modified

        //TODO: move this to the view layer
        foreach ($treatments as $t) {
            $specializationId = is_null($t['specialization_id']) ? 0 : $t['specialization_id'];
            $subSpecializationId = is_null($t['sub_specialization_id']) ? 0 : $t['sub_specialization_id'];
            $treatmentId = is_null($t['treatment_id']) ? 0 : $t['treatment_id'];

//             if ($subSpecializationName == $prevSubSpecializationName) {
//                 $specializationName = $t['specialization_name'];
//             } elseif ($subSpecializationName) {
//                 $prevSubSpecializationName = $subSpecializationName;
//             }

            //If treatment is the same add specialization to the label
            $specializationName = '';
            $treatmentName = $t['treatment_name'];
            if ($treatmentName == $prevTreatmentName) {
                $specializationName = $t['specialization_name'];
            } elseif ($treatmentName) {
                $prevTreatmentName = $treatmentName;
            }

            if ($specializationName == $treatmentName) {
                $label = $treatmentName;
            } elseif ($specializationName) {
                $label = $treatmentName . ' in ' . $specializationName;
            } else {
                $label = $treatmentName;
            }

            $result[] = array(
                'label' => $label,
                'value' => $specializationId.'-'.$subSpecializationId . '-' . $treatmentId . '-' . $t['treatment_type']
            );
        }

        return $result;
    }

    private function getDestinationsByName(SearchParameterBag $searchParams)
    {
        $result = array();

        $destinations = null;

        if ($searchParams->get('treatmentType')) {
            if ($searchParams->get('filter') == SearchParameterBag::FILTER_COUNTRY) {
                $destinations = $this->searchCountriesByNameWithTreatment($searchParams);
            } elseif ($searchParams->get('filter') == SearchParameterBag::FILTER_CITY) {
                $destinations = $this->searchCitiesByNameWithTreatment($searchParams);
            } else {
                $destinations = $this->searchDestinationsByNameWithTreatment($searchParams);
            }
        } else {
            $destinations = $this->searchDestinationsByName($searchParams);
        }

        //TODO: move this to the view layer
        foreach ($destinations as $d) {
            //concatenate city and country names if they are both present only if not FILTERED by city
            if (isset($d['city_name']) && ($searchParams->get('filter') == SearchParameterBag::FILTER_CITY)) {
                $label = $d['city_name'];
            } else {
                $label = $d['city_name'] ? $d['city_name'].', '.$d['country_name'] : $d['country_name'];
            }

            $value = $d['country_id'].'-'.$d['city_id'];

            $result[] = array('label' => $label, 'value' => $value);
        }

        return $result;
    }

    /**
     * Searches treatments (specializations, subspecializations and treatment) by name.
     *
     * @return array
     */
    private function searchTreatmentsByName($searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $cityId = $searchParams->get('cityId', 0);
        $countryId = $searchParams->get('countryId', 0);

        $extendedDestinationWhereClause = ' ';
        if ($cityId) {
            $extendedDestinationWhereClause .= " AND h.city_id = :cityId ";
        } elseif ($countryId) {
            $extendedDestinationWhereClause .= " AND h.country_id = :countryId ";
        }

        $term = $searchParams->get('term');

        //select
        //  treatments with subspecializations
        //  union
        //  treatments with no subspecializations(TODO: is query for this part correct?)
        //  union
        //  subspecializations
        //  union
        //  specializations
        $sql ="
            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        f.name AS specialization_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            INNER JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON h.id = g.institution_id
            WHERE c.name LIKE :name
            AND d.treatment_id IS NOT NULL
            AND g.status = :imcStatus
            AND h.status = :institutionStatus
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        c.id AS treatment_id,
                        c.name AS treatment_name,
                        f.name AS specialization_name,
                        'treatment' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            INNER JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON h.id = g.institution_id
            WHERE c.name LIKE :name
            AND g.status = :imcStatus
            AND d.treatment_id IS NULL
            AND h.status = :institutionStatus
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        e.id AS sub_specialization_id,
                        0 AS treatment_id,
                        e.name AS treatment_name,
                        f.name AS specialization_name,
                        'subSpecialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
            LEFT JOIN treatments AS c ON b.treatment_id = c.id
            LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
            LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            INNER JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON h.id = g.institution_id
            WHERE e.name LIKE :name
            AND g.status = :imcStatus
            AND h.status = :institutionStatus
            $extendedDestinationWhereClause

            UNION

            SELECT
                        f.id AS specialization_id,
                        0 AS sub_specialization_id,
                        0 AS treatment_id,
                        f.name AS treatment_name,
                        f.name AS specialization_name,
                        'specialization' AS treatment_type
            FROM institution_specializations AS a
            LEFT JOIN specializations AS f ON a.specialization_id = f.id
            INNER JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
            LEFT JOIN institutions AS h ON h.id = g.institution_id
            WHERE f.name LIKE :name
            AND g.status = :imcStatus
            AND h.status = :institutionStatus
            $extendedDestinationWhereClause

            GROUP BY treatment_name
            ORDER BY treatment_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$term.'%');
        $stmt->bindValue('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus());
        $stmt->bindValue('imcStatus', InstitutionMedicalCenterStatus::APPROVED);

        if ($cityId) {
            $stmt->bindValue('cityId', $cityId);
        } elseif ($countryId) {
            $stmt->bindValue('countryId', $countryId);
        }
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
     * @deprecated Functionality merged with searchTreatmentsByName()
     */
    private function searchTreatmentsByNameWithDestination(SearchParameterBag $searchParams)
    {
        return $this->searchTreatmentsByName($searchParams);

//         $connection = $this->entityManager->getConnection();
//         $cityId = $searchParams->get('cityId', 0);
//         $countryId = $searchParams->get('countryId', 0);

//         $extendedDestinationWhereClause = ' ';
//         if ($cityId) {
//             $extendedDestinationWhereClause .= " AND h.city_id = :cityId ";
//         } else if ($countryId) {
//             $extendedDestinationWhereClause .= " AND h.country_id = :countryId ";
//         }

//         $sql ="
//             SELECT
//                         f.id AS specialization_id,
//                         e.id AS sub_specialization_id,
//                         c.id AS treatment_id,
//                         c.name AS treatment_name,
//                         'treatment' AS treatment_type
//             FROM institution_specializations AS a
//             LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
//             LEFT JOIN treatments AS c ON b.treatment_id = c.id
//             LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
//             LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
//             LEFT JOIN specializations AS f ON a.specialization_id = f.id
//             LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
//             LEFT JOIN institutions AS h ON g.institution_id = h.id
//             WHERE c.name LIKE :name

//             AND h.status = :institutionStatus
//             $extendedDestinationWhereClause

//             UNION

//             SELECT
//                         f.id AS specialization_id,
//                         0 AS sub_specialization_id,
//                         c.id AS treatment_id,
//                         c.name AS treatment_name,
//                         'treatment' AS treatment_type
//             FROM institution_specializations AS a
//             LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
//             LEFT JOIN treatments AS c ON b.treatment_id = c.id
//             LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
//             LEFT JOIN specializations AS f ON a.specialization_id = f.id
//             LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
//             LEFT JOIN institutions AS h ON g.institution_id = h.id
//             WHERE d.sub_specialization_id IS NULL
//             AND h.status = :institutionStatus
//             AND c.name LIKE :name
//             $extendedDestinationWhereClause

//             UNION

//             SELECT
//                         f.id AS specialization_id,
//                         e.id AS sub_specialization_id,
//                         0 AS treatment_id,
//                         e.name AS treatment_name,
//                         'subSpecialization' AS treatment_type
//             FROM institution_specializations AS a
//             LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
//             LEFT JOIN treatments AS c ON b.treatment_id = c.id
//             LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
//             LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
//             LEFT JOIN specializations AS f ON a.specialization_id = f.id
//             LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
//             LEFT JOIN institutions AS h ON g.institution_id = h.id
//             WHERE e.name LIKE :name
//             AND h.status = :institutionStatus
//             $extendedDestinationWhereClause

//             UNION

//             SELECT
//                         f.id AS specialization_id,
//                         0 AS sub_specialization_id,
//                         0 AS treatment_id,
//                         f.name AS treatment_name,
//                         'specialization' AS treatment_type
//             FROM institution_specializations AS a
//             LEFT JOIN institution_treatments AS b ON a.id = b.institution_specialization_id
//             LEFT JOIN treatments AS c ON b.treatment_id = c.id
//             LEFT JOIN treatment_sub_specializations AS d ON c.id = d.treatment_id
//             LEFT JOIN sub_specializations AS e ON d.sub_specialization_id = e.id
//             LEFT JOIN specializations AS f ON a.specialization_id = f.id
//             LEFT JOIN institution_medical_centers AS g ON a.institution_medical_center_id = g.id
//             LEFT JOIN institutions AS h ON g.institution_id = h.id
//             WHERE f.name LIKE :name
//             AND h.status = :institutionStatus
//             $extendedDestinationWhereClause

//             GROUP BY treatment_name
//             ORDER BY treatment_name ASC
//         ";

//         $stmt = $connection->prepare($sql);
//         $stmt->bindValue('name', '%'.$searchParams->get('term').'%');
//         $stmt->bindValue('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus());

//         if ($cityId) {
//             $stmt->bindValue('cityId', $cityId);
//         } elseif ($countryId) {
//             $stmt->bindValue('countryId', $countryId);
//         }

//         $stmt->execute();

//         return $stmt->fetchAll();
    }

    private function searchDestinationsByName(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $sql ="
            SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
            FROM cities AS a
            LEFT JOIN countries AS b ON a.country_id = b.id
            LEFT JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
            INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
            INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
            WHERE c.status = :institutionStatus
            AND d.status = :imcStatus
            AND (a.name LIKE :name OR b.name LIKE :name)

            UNION

            SELECT 0, '', a.id, a.name
            FROM countries AS a
            LEFT JOIN institutions AS b ON a.id = b.country_id
            INNER JOIN institution_medical_centers AS c ON b.id = c.institution_id
            INNER JOIN institution_specializations AS d ON c.id = d.institution_medical_center_id
            WHERE b.status = :institutionStatus
            AND c.status = :imcStatus
            AND a.name LIKE :name

            ORDER BY city_name ASC, country_name ASC
        ";

        $stmt = $connection->prepare($sql);
        $stmt->bindValue('name', '%'.$searchParams->get('term').'%');
        $stmt->bindValue('institutionStatus', InstitutionStatus::getBitValueForActiveAndApprovedStatus());
        $stmt->bindValue('imcStatus', InstitutionMedicalCenterStatus::APPROVED);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Searches destinations (city/country) by $destinationTerm where the treatment
     * with $treatmentId is available.
     *
     * @param SearchParameterBag $searchParams Search parameters
     *
     * TODO: using prepared statements will return empty results. investigate further;
     * for now don't parameterize the query
     *
     * If we switch the order input "thailand" then "acne scar removal" will show up
     * Possibly the query for searchTreatmentsWithDestination() is also buggy.
     */
    private function searchDestinationsByNameWithTreatment(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();
        $treatmentId = null;

        $name = '"%'.$searchParams->get('term').'%"';
        $institutionStatus = InstitutionStatus::getBitValueForActiveAndApprovedStatus();
        $imcStatus = InstitutionMedicalCenterStatus::APPROVED;

        switch ($searchParams->get('treatmentType')) {
            case 'specialization':

                $treatmentId = $searchParams->get('specializationId');
                $sql = "
                    (SELECT a.id AS city_id, a.name AS city_name, b.id AS country_id, b.name AS country_name
                    FROM cities AS a
                    LEFT JOIN countries AS b ON a.country_id = b.id
                    INNER JOIN institutions AS c ON a.id = c.city_id AND b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    WHERE (a.name LIKE $name OR b.name LIKE $name)
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND e.specialization_id = $treatmentId)

                    UNION

                    (SELECT 0, '', a.id, a.name
                    FROM countries AS a
                    INNER JOIN institutions AS b ON a.id = b.country_id
                    INNER JOIN institution_medical_centers AS c ON b.id = c.institution_id
                    INNER JOIN institution_specializations AS e ON c.id = e.institution_medical_center_id
                    WHERE a.name LIKE $name
                    AND c.status = $imcStatus
                    AND b.status = $institutionStatus
                    AND e.specialization_id = $treatmentId)

                    ORDER BY city_name ASC, country_name ASC
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
                    WHERE (a.name LIKE $name OR b.name LIKE $name)
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND h.sub_specialization_id = $treatmentId

                    UNION

                    SELECT 0, '', a.id, a.name
                    FROM countries AS b
                    LEFT JOIN institutions AS c ON b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    INNER JOIN institution_treatments AS f ON f.specialization_id = e.id
                    LEFT JOIN treatments AS g ON f.treatment_id = g.id
                    LEFT JOIN treatment_sub_specializations AS h ON g.id = h.treatment_id
                    WHERE b.name LIKE $name
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND h.sub_specialization_id = $treatmentId

                    ORDER BY city_name ASC, country_name ASC
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
                    WHERE (a.name LIKE $name OR b.name LIKE $name)
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND f.treatment_id = $treatmentId

                    UNION

                    SELECT 0, '', a.id, a.name
                    FROM countries AS b
                    INNER JOIN institutions AS c ON b.id = c.country_id
                    INNER JOIN institution_medical_centers AS d ON c.id = d.institution_id
                    INNER JOIN institution_specializations AS e ON d.id = e.institution_medical_center_id
                    INNER JOIN institution_treatments AS f ON f.specialization_id = e.id
                    WHERE b.name LIKE $name
                    AND c.status = $institutionStatus
                    AND d.status = $imcStatus
                    AND f.treatment_id = $treatmentId

                    ORDER BY city_name ASC, country_name ASC
                ";
                break;
        }

        $stmt = $connection->executeQuery($sql);

        //TODO: use prepared statements. there seems to be a bug though: the code
        //below will return an empty result
        //$stmt = $connection->prepare($sql);
        //$stmt->bindValue('name', '"%'.$searchParams->get('term').'%"');
        //$stmt->bindValue('treatmentId', $treatmentId, \PDO::PARAM_INT);
        //$stmt->bindValue('institutionStatus', InstitutionStatus::INACTIVE, \PDO::PARAM_INT);

        return $stmt->fetchAll();
    }

    private function searchCountriesByNameWithTreatment(SearchParameterBag $searchParams)
    {
        $connection = $this->container->get('doctrine')->getEntityManager()->getConnection();

        $treatmentId = null;
        $name = '"%'.$searchParams->get('term').'%"';
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
        $name = '"%'.$searchParams->get('term').'%"';
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