<?php
/**
 *
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\EventDispatcher\GenericEvent;

use HealthCareAbroad\MailerBundle\Event\MailerBundleEvents;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyBusinessHourType;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMediaService;

use HealthCareAbroad\PagerBundle\Pager;

use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\AdminBundle\Form\InstitutionFormType;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\HelperBundle\Entity\GlobalAward;

use HealthCareAbroad\InstitutionBundle\Form\Transformer\InstitutionGlobalAwardExtraValueDataTransformer;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardsSelectorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\TreatmentBundle\Entity\Specialization;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\DoctorBundle\Form\DoctorFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationSelectorFormType;

use HealthCareAbroad\MediaBundle\Services\MediaService;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;

use HealthCareAbroad\HelperBundle\Form\CommonDeleteFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterBusinessHourFormType;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionGlobalAwardFormType;

use HealthCareAbroad\AdminBundle\Form\InstitutionMedicalCenterFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionMedicalCenterService;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for handling actions related to InstitutionMedicalCenter and treatments
 *
 * @author Allejo Chris G. Velarde
 *
 */
class InstitutionTreatmentsController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Institution
     */
    private $institution;

    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter;

    public function preExecute()
    {
        $this->request = $this->getRequest();
        $this->institution = $this->get('services.institution.factory')->findById($this->request->get('institutionId', 0));
        // Check Institution
        if(!$this->institution) {
            throw $this->createNotFoundException('Invalid Institution');
        }

        // check InstitutionMedicalCenter
        if ($institutionMedicalCenterId = $this->request->get('imcId', 0)) {
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->findById($institutionMedicalCenterId);
            if (!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institution medical center');
            }

        }
    }

}
