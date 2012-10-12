<?php
namespace HealthCareAbroad\InstitutionBundle\Services;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Repository\InstitutionMedicalCenterRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Service class for InstitutionMedicalCenter
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionMedicalCenterService
{
    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->repository = $this->doctrine->getRepository('InstitutionBundle:InstitutionMedicalCenter');
    }

    /**
     * Get InstitutionTreatments of an InstitutionMedicalCenter
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function getInstitutionTreatmentsOfCenter(InstitutionMedicalCenter $institutionMedicalCenter)
    {
        return $this->doctrine->getRepository('InstitutionBundle:InstitutionTreatment')->getByInstitutionMedicalCenter($institutionMedicalCenter);
    }

    /**
     * Remove draft institution specialization and associated procedure types
     *
     * @param Institution $institution
     * @param int $institutionMedicalCenterId
     * @throws Exception
     * @return InstitutionMedicalCenter $institutionMedicalCenter
     */
    public function deleteDraftInstitutionMedicalCenter(Institution $institution, $institutionMedicalCenterId)
    {
        $em = $this->doctrine->getEntityManager();

        //TODO: check that the institution owns the center.
        $center = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($institutionMedicalCenterId);

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
}