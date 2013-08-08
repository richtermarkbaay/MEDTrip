<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService;

use HealthCareAbroad\HelperBundle\Services\PageMetaConfigurationService;

use HealthCareAbroad\HelperBundle\Entity\PageMetaConfiguration;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionMedicalCenterController extends ResponseHeadersController
{
    protected $institutionMedicalCenter;

    protected $institution;
    
    /**
     * @var InstitutionMedicalCenterApiService
     */
    protected $apiInstitutionMedicalCenterService;

//     public function preExecute()
//     {
//         $request = $this->getRequest();

//         if($request->get('imcSlug', null)) {

//             $slug = $request->get('imcSlug');
//             $this->institutionMedicalCenter = $this->get('services.institution_medical_center')->getFullInstitutionMedicalCenterBySlug($slug);

//             if(!$this->institutionMedicalCenter) {
//                 throw $this->createNotFoundException('Invalid institutionMedicalCenter');
//             }

//             $this->institution = $this->institutionMedicalCenter->getInstitution();

//             $twigService = $this->get('twig');
//             $twigService->addGlobal('institution', $this->institution);
//             $twigService->addGlobal('institutionMedicalCenter', $this->institutionMedicalCenter);
//         }
//         else {

//             throw $this->createNotFoundException('Medical center slug required.');
//         }
//     }

    public function profileAction(Request $request)
    {
        $start = \microtime(true);
        $this->apiInstitutionMedicalCenterService = $this->get('services.api.institutionMedicalCenter');
        $slug = $request->get('imcSlug', null);
        $institutionMedicalCenterId = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')
            ->getInstitutionMedicalCenterIdBySlug($slug);
        
        $memcacheKey = 'frontend.controller.institutionMedicalCenter:profile.'.$institutionMedicalCenterId;
        $memcacheService = $this->get('services.memcache');
        $cachedData = $memcacheService->get($memcacheKey);
        if (!$cachedData) {
            $this->institutionMedicalCenter = $this->apiInstitutionMedicalCenterService->getInstitutionMedicalCenterPublicDataById($institutionMedicalCenterId);
            $this->institution = $this->institutionMedicalCenter['institution'];
            
            if ($this->get('services.api.institution')->isSingleCenterInstitutionType($this->institution)) {
                // redirect to hospital page
            }
            
            // build optional data
            $this->apiInstitutionMedicalCenterService
                ->buildBusinessHours($this->institutionMedicalCenter)
                ->buildDoctors($this->institutionMedicalCenter)
                ->buildGlobalAwards($this->institutionMedicalCenter)
                ->buildOfferedServices($this->institutionMedicalCenter)
                ->buildInstitutionSpecializations($this->institutionMedicalCenter)
                ->buildLogoSource($this->institutionMedicalCenter)
            ;
            
            $specializationsList = $this->apiInstitutionMedicalCenterService->listActiveSpecializations($this->institutionMedicalCenter);
            $this->institutionMedicalCenter['specializationsList'] = $specializationsList;
            
            // cache this processed data
            $memcacheService->set($memcacheKey, $this->institutionMedicalCenter);
        }
        else {
            $this->institutionMedicalCenter = $cachedData;
            $this->institution = $this->institutionMedicalCenter['institution'];
            $specializationsList = $this->institutionMedicalCenter['specializationsList'];
        }
        
        $params = array(
            'awards' => $this->institutionMedicalCenter['globalAwards'],
            'services' => $this->institutionMedicalCenter['offeredServices'],
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institution' => $this->institution,
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry() )->createView(),
            'formId' => 'imc_inquiry_form'
        );
        
        // set request variables to be used by page meta components
        $this->getRequest()->attributes->add(array(
        'institutionMedicalCenter' => $this->institutionMedicalCenter,
        'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION_MEDICAL_CENTER,
        'pageMetaVariables' => array(
            PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($specializationsList),
            // get the first 10 as list
            PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($specializationsList,0, 10, true))
        )));
        
        $content = $this->render('FrontendBundle:InstitutionMedicalCenter:profile.html.twig', $params);
        //$end = \microtime(true); $diff = $end-$start; echo "{$diff}s"; exit;

        return $this->setResponseHeaders($content);
    }
}