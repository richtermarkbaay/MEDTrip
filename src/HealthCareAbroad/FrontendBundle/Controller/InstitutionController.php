<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\ApiBundle\Services\InstitutionApiService;

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
    /**
     * @var array
     */
    protected $institution;
    
    /**
     * @var InstitutionApiService
     */
    private $apiInstitutionService;

//     public function preExecute()
//     {
//         $request = $this->getRequest();
//         $this->apiInstitutionService = $this->get('services.api.institution');

//         if($slug = $request->get('institutionSlug')) {
//             $url = $this->generateUrl('api_institution_getBySlug', array('slug' => $slug), true);
            
// //             $guzzle = new \Guzzle\Service\Client();
// //             $response = $guzzle->get($url)->send();
// //             $institution = $response->getBody(true);
            
// //             $this->institution = file_get_contents($url);
// //             $response = $this->forward('ApiBundle:Institution:getBySlug', array('slug' => $slug));
            
//             $this->institution = $this->apiInstitutionService->findBySlug($slug);
//             //$this->institution = $this->get('services.institution')->getFullInstitutionBySlug($slug);

//             if(!$this->institution) {
//                 throw $this->createNotFoundException('Invalid institution');
//             }
//         }

//     }

    public function profileAction(Request $request)
    {
        $start = \microtime(true);
        $this->apiInstitutionService = $this->get('services.api.institution');
        $this->institution = $this->apiInstitutionService->buildInstitutionPublicDataBySlug($request->get('institutionSlug', null));
        
        
        $params = array(
            'institution' => $this->institution,
            'isSingleCenterInstitution' => $this->apiInstitutionService->isSingleCenterInstitutionType($this->institution['type']),
            'institutionDoctors' => $this->institution['doctors'],
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView(),
            'formId' => 'institution_inquiry_form',
            'institutionAwards' => $this->institution['globalAwards'],
            'institutionServices' => $this->institution['offeredServices'],
        );
        
        // build logo
        

        if($params['isSingleCenterInstitution']) {
//             $centerService = $this->get('services.institution_medical_center');
//             $params['institutionMedicalCenter'] = $institutionService->getFirstMedicalCenter($this->institution);
            
        } else {
            
        }
        
//         // set request variables to be used by page meta components
//         $this->getRequest()->attributes->add(array(
//             'institution' => $this->institution,
//             'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION,
//             'pageMetaVariables' => array(
//                 PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE => $institutionService->countActiveMedicalCenters($this->institution),
//                 PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($specializationsList),
//                 // get the first 10 as list
//                 PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($specializationsList,0, 10, true))
//         )));

        //$start = microtime(true);
        $content = $this->render('FrontendBundle:Institution:profile.html.twig', $params);
        //echo $content;
        $response= $this->setResponseHeaders($content);
        
        $end = \microtime(true); 
        define('GLOBAL_WATA', $end-$start);
         
        
        
        return $response;
    }

    public function errorReportAction()
    {

    }
}