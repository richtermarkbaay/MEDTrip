<?php
namespace HealthCareAbroad\SearchBundle\Services;

use Symfony\Component\HttpFoundation\ParameterBag;
//TODO: clean up
/**
 * The behavior of this class differs from ParameterBag in that it
 * only allows a defined group of parameter names to be set.
 * This adds special processing and transformation of the passed in parameters.
 *
 * It will also disable several inherited functions.
 *
 */
class SearchParameterBag extends ParameterBag
{
    const SEARCH_TYPE_DESTINATIONS = 1;
    const SEARCH_TYPE_TREATMENTS = 2;
    const SEARCH_TYPE_COMBINATION = 3;

    const FILTER_COUNTRY = 'country';
    const FILTER_CITY = 'city';
    const FILTER_SPECIALIZATION = 'specialization';
    const FILTER_SUBSPECIALIZATION = 'subSpecialization';
    const FILTER_TREATMENT = 'treatment';

    /**
     * Constructor.
     *
     * @param array $parameters An array of parameters
     *
     * @api
     */
    public function __construct(array $parameters = array())
    {
        if (!empty($parameters)) {

            $searchedTerm = null;
            $treatment = null;
            $destination = null;
            $treatmentLabel = '';
            $destinationLabel = '';
            $filter = '';

            // Allow only these keys
            foreach ($parameters as $key => $value) {
                if ('treatment' === $key) {
                    $treatment = $value;
                } else if ('destination' === $key) {
                    $destination = $value;
                } else if ('term' === $key) {
                    $searchedTerm = $value;
                } else if ('treatmentLabel' === $key) {
                    $treatmentLabel = $parameters['treatmentLabel'];
                } else if ('destinationLabel' === $key) {
                    $destinationLabel = $parameters['destinationLabel'];
                } else if ('filter' === $key) {
                    $filter = $parameters['filter'];
                } else {
                    throw new \Exception('Invalid parameter: ' . $key);
                }
            }

            $this->parameters = $this->processParameters($treatment, $treatmentLabel, $destination, $destinationLabel, $searchedTerm, $filter);
        }
    }

    private function processParameters($treatment, $treatmentLabel, $destination, $destinationLabel, $searchedTerm, $filter)
    {
        $context = '';
        $countryId = 0;
        $cityId = 0;
        $treatmentType = '';

        if (!empty($destination)) {
            list($countryId, $cityId) = explode('-', $destination);
        }

        if (!is_numeric($cityId) && !is_numeric($countryId) && !is_numeric($treatment)) {
            throw new \Exception('Invalid id');
        }

        if ($searchedTerm) {
            //TODO: first condition is not enough
            if ($treatmentLabel && $destinationLabel) {
                $context = self::SEARCH_TYPE_COMBINATION;
            } elseif ($treatmentLabel === $searchedTerm || ($countryId || $cityId)) {
                $context = self::SEARCH_TYPE_TREATMENTS;
            } elseif ($destinationLabel === $searchedTerm || ($treatment)) {
                $context = self::SEARCH_TYPE_DESTINATIONS;
            }
        } else {
            if ($countryId || $cityId) {
                $context = $context | self::SEARCH_TYPE_DESTINATIONS;
            }
            if ($treatment) {
                $context = $context | self::SEARCH_TYPE_TREATMENTS;
            }
            if (!$context) {
                if ($treatmentLabel && $destinationLabel) {
                    $context = self::SEARCH_TYPE_COMBINATION;
                } elseif ($treatmentLabel) {
                    $context = self::SEARCH_TYPE_TREATMENTS;
                } elseif ($destinationLabel) {
                    $context = self::SEARCH_TYPE_DESTINATIONS;
                }
            }
        }

        return array(
                        'searchedTerm' => $searchedTerm,
                        'context' => $context,
                        'cityId' => $cityId,
                        'countryId' => $countryId,
                        'treatmentId' => $treatment,
                        'treatmentType' => $treatmentType,
                        'treatmentParameter' => $treatment,
                        'destinationParameter' => $destination,
                        'treatmentLabel' => $treatmentLabel,
                        'destinationLabel' => $destinationLabel,
                        'filter' => $filter
        );
    }


    //TODO: not used
    public function getDynamicRouteParams($doctrine = null)
    {
        $routeParams = array('countryId' => $this->parameters['countryId'], 'cityId' => $this->parameters['cityId']);

        if ($this->parameters['context'] == self::SEARCH_TYPE_TREATMENTS || $this->parameters['context'] == self::SEARCH_TYPE_COMBINATION) {
            if (is_null($doctrine)) {
                throw new \Exception('Doctrine is required.');
            }

            $connection = $doctrine->getConnection();

            // This should only return one row??? or we shouldn't be here if there are more than one type
            $sql = "SELECT * FROM search_terms WHERE term_id = :termId GROUP BY type";

            $stmt = $connection->prepare($sql);
            $stmt->bindValue('termId', $this->parameters['treatmentId']);
            $stmt->execute();

            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $result = $result[0];

            $routeParams = array_merge($routeParams, array(
                'specializationId' => $result['specialization_id'],
                'subSpecializationId' => $result['sub_specialization_id'],
                'treatmentId' => $result['treatment_id']
            ));
        }

        return $routeParams;
    }

    ////////////////////////////////////////////
    // The following functions have been disabled
    ////////////////////////////////////////////

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpFoundation\ParameterBag::replace()
     */
    public function replace(array $parameters = array())
    {
        throw new \Exception('Method disabled.');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpFoundation\ParameterBag::add()
     */
    public function add(array $parameters = array())
    {
        throw new \Exception('Method disabled.');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpFoundation\ParameterBag::set()
     */
    //TODO: temporarily enable this
//     public function set($key, $value)
//     {
//         throw new \Exception('Method disabled.');
//     }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpFoundation\ParameterBag::remove()
     */
    public function remove($key)
    {
        throw new \Exception('Method disabled.');
    }
}