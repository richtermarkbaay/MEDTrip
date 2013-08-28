<?php
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecializationStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Repository\InstitutionSpecializationRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for InstitutionSpecialization
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionSpecializationService
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var InstitutionSpecializationRepository
     */
    private $repository;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization');
    }

    public function prepareMultipleAdd()
    {

    }

    public function save(InstitutionSpecialization $institutionSpecialization)
    {
        $em = $this->doctrine->getEntityManager();
        $em->persist($institutionSpecialization);
        $em->flush();
    }

    /** TODO: DEPRECATED ??
     * Remove draft institution specialization and associated procedure types
     *
     * @param Institution $institution
     * @param int $institutionSpecializationId
     * @throws Exception
     * @return InstitutionSpecialization $institutionSpecialization
     */
    public function deleteDraftInstitutionSpecialization(Institution $institution, $institutionSpecializationId)
    {
        $em = $this->doctrine->getEntityManager();

        //TODO: check that the institution owns the center.
        $center = $em->getRepository('InstitutionBundle:InstitutionSpecialization')->find($institutionSpecializationId);

        if (InstitutionMedicalCenterStatus::DRAFT != $center->getStatus()) {
            throw new Exception('Delete operation not allowed.');
        }

        //NOTE: Supposedly a DRAFT institution specialization will have no procedure types
        // so the loop below will never run. But this specification can change.
        //TODO: Use DQL DELETE statement to delete multiple entities of a type with a single command and without hydrating these entities
        foreach($center->getInstitutionTreatments() as $entity) {
            $em->remove($entity);
        }

        $em->remove($center);
        $em->flush();

        return $center;
    }

    /**
     * Newley added service for updated markup
     * Get all unselected specializations in centers
     *
     * @param Institution $institution
     * @author Chaztine Blance
     */
    public function getNotSelectedSpecializations(Institution $institution){
        $specializations = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitution($institution);

        $result = $this->doctrine->getRepository('TreatmentBundle:Specialization')->getAvailableSpecializations($specializations);

        return $result;
    }

    /**
     *
     * @param InstitutionMedicalCenter $center
     */
    public function getNotSelectedSpecializationsOfInstitutionMedicalCenter(InstitutionMedicalCenter $center)
    {
        $specializations = $this->doctrine->getRepository('InstitutionBundle:InstitutionSpecialization')->getActiveSpecializationsByInstitutionMedicalCenter($center);

        return $this->doctrine->getRepository('TreatmentBundle:Specialization')->getAvailableSpecializations($specializations);
    }
}