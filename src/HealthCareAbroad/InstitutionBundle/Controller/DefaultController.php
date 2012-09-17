<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DefaultController extends InstitutionAwareController
{
    /**
     * @PreAuthorize("hasAnyRole('INSTITUTION_USER')")
     *
     */
    public function indexAction()
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution');

        $draftInstitutionMedicalCenters = $institutionRepository->getDraftInstitutionMedicalCenters($this->institution);

        return $this->render('InstitutionBundle:Default:index.html.twig', array(
                        'draftInstitutionMedicalCenters' => $draftInstitutionMedicalCenters
        ));
    }

    public function error403Action()
    {
        return $this->render('InstitutionBundle:Exception:error403.html.twig');
    }

}
