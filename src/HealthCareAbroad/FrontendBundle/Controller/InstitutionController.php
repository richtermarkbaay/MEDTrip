<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

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
        $this->apiInstitutionService = $this->get('services.api.institution');
        $mediaExtensionService = $this->apiInstitutionService->getMediaExtension(); 
        
        $slug = $request->get('institutionSlug', null);
        $this->institution = $this->apiInstitutionService->getInstitutionPublicDataBySlug($slug);
        
        // build logo        
        $this->apiInstitutionService
            ->buildFeaturedMediaSource($this->institution)
            ->buildLogoSource($this->institution);

        // build logos of the medical centers of this institution
        // TODO: I'm hesitant on placing this on a service,
        // Also hesitant on modifying the twig extension since it is used in many contexts
        $canDisplayImcLogo = $this->institution['payingClient'] == 1;
        foreach ($this->institution['institutionMedicalCenters'] as $key => &$imcData) {
            // client is allowed to display logo, and there is a logo
            if ($canDisplayImcLogo && $imcData['logo']) {
                $imcData['logo']['src'] = $mediaExtensionService->getInstitutionMediaSrc($imcData['logo'], ImageSizes::MEDIUM);
            }
            else {
                // not allowed to display logo, or clinic has no logo
                // we get the logo of the first specialization
                $firstSpecialization = \count($imcData['institutionSpecializations']) 
                    ? (isset($imcData['institutionSpecializations'][0]['specialization']) ? $imcData['institutionSpecializations'][0]['specialization'] : null) 
                    : null;
                
                // first specialization has a media
                if ($firstSpecialization && $firstSpecialization['media']){
                    $imcData['logo']['src'] = $mediaExtensionService->getSpecializationMediaSrc($firstSpecialization['media'], ImageSizes::SPECIALIZATION_DEFAULT_LOGO);
                }
            }
        }
        
        $specializationsList = $this->apiInstitutionService->listActiveSpecializations($this->institution['id']);
        // set request variables to be used by page meta components
        $this->getRequest()->attributes->add(array(
            'institution' => $this->institution,
            'pageMetaContext' => PageMetaConfiguration::PAGE_TYPE_INSTITUTION,
            'pageMetaVariables' => array(
                PageMetaConfigurationService::ACTIVE_CLINICS_COUNT_VARIABLE => \count($this->institution['institutionMedicalCenters']),
                PageMetaConfigurationService::SPECIALIZATIONS_COUNT_VARIABLE => \count($specializationsList),
                // get the first 10 as list
                PageMetaConfigurationService::SPECIALIZATIONS_LIST_VARIABLE => \implode(', ',  \array_slice($specializationsList,0, 10, true))
        )));
        
        $params = array(
            'institution' => $this->institution,
            'isSingleCenterInstitution' => $this->apiInstitutionService->isSingleCenterInstitutionType($this->institution['type']),
            'institutionDoctors' => $this->institution['doctors'],
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView(),
            'formId' => 'institution_inquiry_form',
            'institutionAwards' => $this->institution['globalAwards'],
            'institutionServices' => $this->institution['offeredServices'],
        );
        
        $content = $this->render('FrontendBundle:Institution:profile.html.twig', $params);
        $response= $this->setResponseHeaders($content);
        
//         $end = \microtime(true);
//         define('GLOBAL_WATA', $end-$start);
//         echo GLOBAL_WATA."s"; exit;
        
        return $response;
    }

    public function errorReportAction()
    {

    }
}