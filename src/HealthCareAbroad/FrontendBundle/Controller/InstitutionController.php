<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\ApiBundle\Services\InstitutionMedicalCenterApiService;

use HealthCareAbroad\MediaBundle\Services\ImageSizes;

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
    
    /**
     * @var InstitutionMedicalCenterApiService
     */
    private $apiInstitutionMedicalCenterService;

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

    /**
     * 
     * @param Request $request
     * @return Response
     * @author acgvelarde
     */
    public function profileAction(Request $request)
    {
        $start = \microtime(true);
        $slug = $request->get('institutionSlug', null);
        $institutionId = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->getInstitutionIdBySlug($slug);
        $this->apiInstitutionService = $this->get('services.api.institution');
        $this->apiInstitutionMedicalCenterService = $this->get('services.api.institutionMedicalCenter');
        $memcacheService = $this->get('services.memcache');
        $memcacheKey = 'frontend.controller.institution_profile.'.$institutionId;
        $cachedData = $memcacheService->get($memcacheKey);
        
        if (!$cachedData) {
            
            $mediaExtensionService = $this->apiInstitutionService->getMediaExtension();
            
            $this->institution = $this->apiInstitutionService->getInstitutionPublicDataById($institutionId);
            
            $isSingleCenterInstitution = $this->apiInstitutionService->isSingleCenterInstitutionType($this->institution['type']);
            
            if ($isSingleCenterInstitution) {
                $firstMedicalCenter = isset($this->institution['institutionMedicalCenters'][0])
                    ? $this->institution['institutionMedicalCenters'][0]
                    : null;
                if (!$firstMedicalCenter) {
                    // no medical center
                    // FIXME: right now throw an exception since this should not happen
                    throw $this->createNotFoundException('Invalid single center clinic');
                }
                
                // build awards from the first clinic
                $this->institution['globalAwards'] = $this->apiInstitutionMedicalCenterService->getGlobalAwardsByInstitutionMedicalCenterId($firstMedicalCenter['id']); 
                
                // build offered services from first clinic
                $this->institution['offeredServices'] = $this->apiInstitutionMedicalCenterService->getOfferedServicesByInstitutionMedicalCenterId($firstMedicalCenter['id']);
                
                // build doctors from first clinic
                $this->institution['doctors'] = $this->apiInstitutionMedicalCenterService->getDoctorsByInstitutionMedicalCenterId($firstMedicalCenter['id']);
                
                $this->apiInstitutionMedicalCenterService
                    ->buildInstitutionSpecializations($firstMedicalCenter)
                    ->buildBusinessHours($firstMedicalCenter)
                    ->buildLogoSource($firstMedicalCenter)
                ;
                
                $this->institution['institutionMedicalCenters'][0] = $firstMedicalCenter;
            } 
            // multiple center institution
            else {
                
                $this->apiInstitutionService
                    ->buildDoctors($this->institution) // build doctors data
                    ->buildGlobalAwards($this->institution) // build global awards data
                    ->buildOfferedServices($this->institution) // build anciliary services data
                    ->buildFeaturedMediaSource($this->institution) // build cover photo source
                    ->buildLogoSource($this->institution) // build logo
                ;
                
                // Hesitant on modifying the twig extension since it is used in many contexts
                foreach ($this->institution['institutionMedicalCenters'] as $key => &$imcData) {
                    $this->apiInstitutionMedicalCenterService
                        ->buildInstitutionSpecializations($imcData)
                        ->buildLogoSource($imcData);
                }
            }
            
            $this->institution['specializationsList'] = $this->apiInstitutionService->listActiveSpecializations($this->institution['id']); 
            // cache this processed data
            $memcacheService->set($memcacheKey, $this->institution);
        }
        else {
            $this->institution = $cachedData;
            $isSingleCenterInstitution = $this->apiInstitutionService->isSingleCenterInstitutionType($this->institution['type']);
        }
        
        $firstMedicalCenter = isset($this->institution['institutionMedicalCenters'][0])
            ? $this->institution['institutionMedicalCenters'][0]
            : null;
        
        // set request variables to be used by page meta components
        $this->getRequest()->attributes->add(array(
            'institution' => $this->institution,
            'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION,
            'pageMetaVariables' => array(
                PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE => \count($this->institution['institutionMedicalCenters']),
                PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($this->institution['specializationsList']),
                // get the first 10 as list
                PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($this->institution['specializationsList'],0, 10, true))
        )));        
        
        $params = array(
            'institution' => $this->institution,
            'isSingleCenterInstitution' => $isSingleCenterInstitution,
            'institutionDoctors' => $this->institution['doctors'],
            'institutionMedicalCenter' => $firstMedicalCenter, // will only be used in single center 
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView(),
            'institutionAwards' => $this->institution['globalAwards'],
            'institutionServices' => $this->institution['offeredServices'],
        );        
        
        $content = $this->render('FrontendBundle:Institution:profile.html.twig', $params);
        //exit;
        $response= $this->setResponseHeaders($content);
        
        return $response;
    }

    public function errorReportAction()
    {

    }
}