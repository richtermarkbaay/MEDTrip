<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use Guzzle\Http\Message\Request;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionMedicalCenterController extends ResponseHeadersController
{
    /**
     * @var InstitutionMedicalCenter
     */
    protected $institutionMedicalCenter;

    /**
     * @var Institution
     */
    protected $institution;

    public function preExecute()
    {
        $request = $this->getRequest();

        if($request->get('imcSlug', null)) {

            $slug = $request->get('imcSlug');
            $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->getFullInstitutionMedicalCenterBySlug($slug);

            if(!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institutionMedicalCenter');
            }

            $this->institution = $this->institutionMedicalCenter->getInstitution();

            $twigService = $this->get('twig');
            $twigService->addGlobal('institution', $this->institution);
            $twigService->addGlobal('institutionMedicalCenter', $this->institutionMedicalCenter);
        }
        else {

            throw $this->createNotFoundException('Medical center slug required.');
        }
    }

    public function profileAction($institutionSlug)
    {

        if ($this->get('services.institution')->isSingleCenter($this->institution)) {
            // this should redirect to institution profile page if medical center's institution is a single center type
        }

        $centerService = $this->get('services.institution_medical_center');
        $params = array(
            'awards' => $centerService->getMedicalCenterGlobalAwards($this->institutionMedicalCenter),
            'services' => $centerService->getMedicalCenterServices($this->institutionMedicalCenter),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry() )->createView(),
            'formId' => 'imc_inquiry_form'
        );
        
        $specializationsList = $centerService->listActiveSpecializations($this->institutionMedicalCenter);
        
        // set request variables to be used by page meta components
        $this->getRequest()->attributes->add(array(
        'institutionMedicalCenter' => $this->institutionMedicalCenter,
        'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION_MEDICAL_CENTER,
        'pageMetaVariables' => array(
            PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($specializationsList),
            // get the first 10 as list
            PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($specializationsList,0, 10, true))
        )));

        return $this->setResponseHeaders($this->render('FrontendBundle:InstitutionMedicalCenter:profile.html.twig', $params));
    }
}