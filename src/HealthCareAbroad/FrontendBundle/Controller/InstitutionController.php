<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;
use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;
use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionController extends ResponseHeadersController
{
    protected $institution;

    public function preExecute()
    {
        $request = $this->getRequest();

        if($slug = $request->get('institutionSlug')) {
            $this->institution = $this->get('services.institution')->getFullInstitutionBySlug($slug);

            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid institution');
            }
        }

    }

    public function profileAction($institutionSlug)
    {
//         echo "test";exit;
        $institutionService = $this->get('services.institution');
        $gallery = $this->institution->getGallery();

        $params = array(
            'institution' => $this->institution,
            'isSingleCenterInstitution' => $institutionService->isSingleCenter($this->institution),
            'institutionDoctors' => $institutionService->getAllDoctors($this->institution),
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView(),
            'formId' => 'institution_inquiry_form'

//            'institutionBranches' => $institutionService->getBranches($this->institution)
        );

        if($params['isSingleCenterInstitution']) {
            $centerService = $this->get('services.institution_medical_center');
            $params['institutionMedicalCenter'] = $institutionService->getFirstMedicalCenter($this->institution);
            $params['institutionAwards'] = $centerService->getMedicalCenterGlobalAwards($params['institutionMedicalCenter']);//echo "test";exit;
            $params['institutionServices'] = $centerService->getMedicalCenterServices($params['institutionMedicalCenter']);
        } else {//echo "test2";exit;
            $params['institutionAwards'] = $institutionService->getAllGlobalAwards($this->institution);
            $params['institutionServices'] = $institutionService->getInstitutionServices($this->institution);
        }
        
        $specializationsList = $institutionService->listActiveSpecializations($this->institution);
        
        // set request variables to be used by page meta components
        $this->getRequest()->attributes->add(array(
            'institution' => $this->institution,
            'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION,
            'pageMetaVariables' => array(
                PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE => $institutionService->countActiveMedicalCenters($this->institution),
                PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($specializationsList),
                // get the first 10 as list
                PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($specializationsList,0, 10, true))
        )));

        return $this->setResponseHeaders($this->render('FrontendBundle:Institution:profile.html.twig', $params));
    }

    public function errorReportAction()
    {

    }
}