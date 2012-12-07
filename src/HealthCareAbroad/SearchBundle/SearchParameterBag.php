<?php
namespace HealthCareAbroad\SearchBundle;

use JMS\SecurityExtraBundle\Security\Authorization\Expression\Compiler\Func\IsFullyAuthenticatedFunctionCompiler;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * The behavior of this class differs from ParameterBag in that it
 * only allows a defined group of parameter names to be set. This
 * adds special processing and transformation of the passed in parameters.
 *
 * It will also disable several inherited functions.
 */
class SearchParameterBag extends ParameterBag
{
    const SEARCH_TYPE_DESTINATIONS = 1;
    const SEARCH_TYPE_TREATMENTS = 2;
    const SEARCH_TYPE_COMBINATION = 3;

    private $country = 1;
    private $city = 2;
    private $specialization = 4;
    private $subSpecialization = 8;
    private $treatment = 16;

    /**
     * Constructor.
     *
     * @param array $parameters An array of parameters
     *
     * @api
     */
    public function __construct(array $parameters = array())
    {
        $term = null;
        $treatment = null;
        $destination = null;

        // Allow only these keys
        foreach ($parameters as $key => $value) {
            if ('treatment' === $key) {
                $treatment = $value;
            } else if ('destination' === $key) {
                $destination = $value;
            } else if ('term' === $key) {
                $term = $value;
            } else {
                throw new \Exception('Invalid parameter: ' . $key);
            }
        }

        $this->parameters = $this->processParameters($treatment, $destination, $term);
    }

    private function processParameters($treatment, $destination, $term)
    {
        $context = 0;
        $treatmentType = '';
        $specializationId = 0;
        $subSpecializationId = 0;
        $treatmentId = 0;
        $countryId = 0;
        $cityId = 0;

        if (!empty($treatment)) {
            list($specializationId, $subSpecializationId, $treatmentId, $treatmentType) = explode('-', $treatment);
        }

        if (!empty($destination)) {
            list($countryId, $cityId) = explode('-', $destination);
        }

        if (!is_numeric($cityId) && !is_numeric($countryId) &&
                        !is_numeric($specializationId) &&
                        !is_numeric($subSpecializationId) &&
                        !is_numeric($treatmentId)) {
            throw new \Exception('Invalid id');
        }

        if ($countryId || $cityId) {
            $context = $context | self::SEARCH_TYPE_DESTINATIONS;
        }
        if ($specializationId || $subSpecializationId || $treatmentId) {
            $context = $context | self::SEARCH_TYPE_TREATMENTS;
            //TODO: will we still need to set the treatment type in case this is
            //not available? or will there be cases where it is not possible to
            //know the type beforehand?
        }

        return array(
                        'term' => $term,
                        'context' => $context,
                        'cityId' => $cityId,
                        'countryId' => $countryId,
                        'treatmentType' => $treatmentType,
                        'treatmentId' => $treatmentId,
                        'specializationId' => $specializationId,
                        'subSpecializationId' => $subSpecializationId,
                        'treatmentParameter' => $treatment,
                        'destinationParameter' => $destination
        );
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
    public function set($key, $value)
    {
        throw new \Exception('Method disabled.');
    }

    /**
     * (non-PHPdoc)
     * @see \Symfony\Component\HttpFoundation\ParameterBag::remove()
     */
    public function remove($key)
    {
        throw new \Exception('Method disabled.');
    }
}