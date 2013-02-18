<?php
namespace HealthCareAbroad\SearchBundle\Services\SearchStrategy;

use HealthCareAbroad\SearchBundle\Services\SearchParameterBag;
use Symfony\Component\DependencyInjection\ContainerAware;

abstract class SearchStrategy extends ContainerAware
{
    const RESULT_TYPE_ARRAY = 0;
    const RESULT_TYPE_OBJECT = 1;
    const RESULT_TYPE_INTERNAL = 2;

    protected $results;

    protected $resultType;

    protected $isViewReadyResults;

    abstract public function isAvailable();

    /**
     *
     * @param SearchParameterBag $searchParam
     */
    abstract public function search(SearchParameterBag $searchParam);

    abstract public function getArrayResults();

    abstract public function getObjectResults();

    public function getInternalResults()
    {
        return $this->resultType;
    }

    /**
     *
     * @param int $resultType
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;
    }

    public function isViewReadyResults()
    {
        return $this->isViewReadyResults;
    }

    // NOTE: The following functions may be better suited residing in the
    // the extending class. Right now, it seems that the implementation will be
    // similar independent of whichever concrete search strategy is used.

    public function searchInstitutionsByCountry($country)
    {
        //$result = $this->container->get('doctrine')->getEntityManager()->getRepository('InstitutionBundle:Institution')->getInstitutionsByCountry($country);
        $result = $this->container->get('doctrine')->getEntityManager()->getRepository('TermBundle:SearchTerm')->findByCountry($country);

        return $result;
    }

    public function searchInstitutionsByCity($city)
    {
        //$result = $this->container->get('doctrine')->getEntityManager()->getRepository('InstitutionBundle:Institution')->getInstitutionsByCity($city);
        $result = $this->container->get('doctrine')->getEntityManager()->getRepository('TermBundle:SearchTerm')->findByCity($city);

        return $result;
    }

    public function searchMedicalCentersBySpecialization($specialization)
    {
        //$result = $this->container->get('doctrine')->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySpecialization($specialization);
        $result = $this->container->get('doctrine')->getEntityManager()->getRepository('TermBundle:SearchTerm')->findBySpecialization($specialization);

        return $result;
    }

    public function searchMedicalCentersBySubSpecialization($subSpecialization)
    {
        //$result = $this->container->get('doctrine')->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersBySubSpecialization($subSpecialization);
        $result = $this->container->get('doctrine')->getRepository('TermBundle:SearchTerm')->findBySubSpecialization($subSpecialization);

        return $result;
    }

    public function searchMedicalCentersByTreatment($treatment)
    {
        //$result = $this->container->get('doctrine')->getEntityManager()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->getMedicalCentersByTreatment($treatment);
        $result = $this->container->get('doctrine')->getEntityManager()->getRepository('TermBundle:SearchTerm')->findByTreatment($treatment);

        return $result;
    }

    public function searchMedicalCentersByTerm($term)
    {
        $result = $this->container->get('doctrine')->getRepository('TermBundle:SearchTerm')->findByTerm($term);

        return $result;
    }

    public function searchMedicalCentersByTerms($termIds = array(), array $filters = array())
    {
        $result = $this->container->get('doctrine')->getRepository('TermBundle:SearchTerm')->findByTerms($termIds, $filters);

        return $result;
    }
}