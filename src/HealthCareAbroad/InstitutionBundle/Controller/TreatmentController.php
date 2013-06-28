<?php 

namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionTreatmentProcedureEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionTreatmentProcedureEvent;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionTreatmentEvents;

use HealthCareAbroad\InstitutionBundle\Event\CreateInstitutionTreatmentEvent;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentProcedureFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class TreatmentController extends InstitutionAwareController
{
    /**
     * @var InstitutionSpecialization
     */
    private $institutionSpecialization;
    
    public function preExecute()
    {
        parent::preExecute();
        $isId = $this->getRequest()->get('isId', 0);
        if ($isId) {
            $this->institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($isId);
            if (!$this->institutionSpecialization) {
                throw $this->createNotFoundException("Invalid institution specialization");
            }    
        }
    }
}