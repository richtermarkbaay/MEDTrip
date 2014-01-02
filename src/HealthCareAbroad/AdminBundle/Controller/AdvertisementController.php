<?php
/**
 * 
 * @author Adelbert D. Silla
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementTypes;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementMediaService;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementStatuses;

use HealthCareAbroad\MediaBundle\Entity\Media;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;
use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;



class AdvertisementController extends Controller
{
    
    /**
     * @var Advertisement
     */
    private $advertisement;
        
    /**
     * @var Institution
     */
    private $institution;
    

    public function preExecute()
    {
        $ad = $this->getRequest()->get('advertisement'); 
        $institutionId = $ad ? $ad['institution'] : null;

        if ($advertisementId = $this->getRequest()->get('advertisementId', 0)) {
            $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();            
            $qb->select('ad, a, b, c, d, e, f')->from('AdvertisementBundle:Advertisement', 'ad')
               ->leftJoin('ad.institution', 'a')
               ->leftJoin('a.institutionMedicalCenters', 'b')
               ->leftJoin('b.institutionSpecializations', 'c')
               ->leftJoin('c.specialization', 'd')
               ->leftJoin('c.treatments', 'e')
               ->leftJoin('e.subSpecializations', 'f')
               ->where('ad.id = :advertisementId')
               ->setParameter('advertisementId', $advertisementId);

            $this->advertisement = $qb->getQuery()->getOneOrNullResult();

            if (!$this->advertisement) {
                throw $this->createNotFoundException("Invalid advertisement.");
            }

            $this->institution = $this->advertisement->getInstitution();
        }

        if (($institutionId = $this->getRequest()->get('institutionId', $institutionId)) && !$this->advertisement) {

            $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f')->from('InstitutionBundle:Institution', 'a')
               ->leftJoin('a.institutionMedicalCenters', 'b')
               ->leftJoin('b.institutionSpecializations', 'c')
               ->leftJoin('c.specialization', 'd')
               ->leftJoin('c.treatments', 'e')
               ->leftJoin('e.subSpecializations', 'f')
               ->where('a.id = :institutionId')
               ->setParameter('institutionId', $institutionId);
            
            $this->institution = $qb->getQuery()->getOneOrNullResult();
            
            if (!$this->institution) {
                throw $this->createNotFoundException("Invalid institution.");
            }
        }
    }
    
    /**
     * Management page of Advertisements.
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {   
        $advertisementTypeId = $request->get('advertisementType', 0);

        if ($advertisementTypeId == ListFilter::FILTER_KEY_ALL) {
        	$advertisementTypeId = 0;
        }

        $params = array(
            'advertisementStatus' => AdvertisementStatuses::getList(),
            'advertisementTypeId' => $advertisementTypeId,
            'advertisements' => $this->filteredResult,
            'pager' => $this->pager);

        return $this->render('AdminBundle:Advertisement:index.html.twig', $params);
        
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENTS')")
     * @param Request $request
     */
    public function addAction(Request $request)
    {
        $advertisement = new Advertisement();
        $advertisementTypeId = $request->get('advertisementTypeId', 1);
        $advertisement->setInstitution($this->institution);

        $defaultExpiryDate = new \DateTime();
        $defaultExpiryDate->modify('+30 days');
        $advertisement->setDateExpiry($defaultExpiryDate);

        if($advertisementTypeId) {
            $advertisementType = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementType')->find($advertisementTypeId);
            $advertisement->setAdvertisementType($advertisementType);
        }

        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new AdvertisementFormType($em), $advertisement);
        
        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $this->generateUrl('admin_advertisement_create'),
            'form' => $form->createView(),
            'step' => (int)$request->get('step', 1)
        ));
    }

    /**
     * This is the edit advertisement page
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENTS')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        //$this->advertisement->setInstitution($this->institution);
        $form = $this->createForm(new AdvertisementFormType($em), $this->advertisement);

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $this->generateUrl('admin_advertisement_update', array('advertisementId' => $this->advertisement->getId())),
            'form' => $form->createView(),
            'ads_filesystem' => $this->get('advertisement_filesystem'),
            'isEditMode' => true,
            'step' => (int)$request->get('step', 2)
        ));
    }

    /**
     * This is the step when adding an advertisement
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENTS')")
     * @param Request $request
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        $advertisementData = $request->get('advertisement');

        if(!$request->getMethod() == 'POST') {
            return new Response("Save requires POST method!", 405);
        }

        if(!$this->advertisement) {
            $advertisement = new Advertisement();
            $advertisementType = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementType')->find($advertisementData['advertisementType']);
            $advertisement->setAdvertisementType($advertisementType);

            $formAction = $this->generateUrl('admin_advertisement_create');
        } else {
            $advertisement = $this->advertisement;
            $formAction = $this->generateUrl('admin_advertisement_update', array('advertisementId'=>$advertisement->getId()));
        }

        $advertisement->setInstitution($this->institution);
        
        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new AdvertisementFormType($em), $advertisement);       
        
        $form->bind($request);

        if ($form->isValid()) {
            $this->saveMedia($advertisement);
            $this->get('services.advertisement')->save($advertisement);

            // Invalidate Advertisement cache
            $this->invalidateAdsCache($advertisement);

            $request->getSession()->setFlash("success", "Successfully created advertisement. You may now generate invoice.");

            return $this->redirect($this->generateUrl('admin_advertisement_index'));
        }

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $formAction,
            'form' => $form->createView(),
            'step' => (int)$request->get('step', 2)
        ));
    }
    
    /**
     * This page will be the third step when creating a new advertisement.
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENTS')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addInvoiceAction(Request $request)
    {
        return $this->render('AdminBundle:Advertisement:addInvoice.html.twig', array(
            'advertisement' => $this->advertisement
        ));
    }
    
    /**
     * This will be the last page when creating a new advertisement
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENTS')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction(Request $request)
    {
        return $this->render('AdminBundle:Advertisement:preview.html.twig', array(
            'advertisement' => $this->advertisement
        ));
    }

    private function saveMedia($advertisement)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $fileClassName = 'Symfony\Component\HttpFoundation\File\UploadedFile';
        
        foreach($advertisement->getAdvertisementPropertyValues() as $i => $each) {
            $newValue = null;
            $value = $each->getValue();
            $property = $each->getAdvertisementPropertyName();

            if($property->getName() == 'highlights') {
                if(is_object($value)) {
                    $value = $value->toArray();
                }

                foreach($value as $i => $highlight) {
                    $file = $highlight['icon'];
                    if(is_object($file)) {
                        if($fileClassName == get_class($file)) {
                            $media = $this->get('services.advertisement.media')->upload($file, $advertisement, AdvertisementMediaService::HIGHLIGHT_IMAGE);
                            $value[$i]['icon'] = $media ? $this->mediaObjectToArray($media) : array(); 
                        } else {
                            $value[$i]['icon'] = $this->mediaObjectToArray($file);
                        }
                    }
                }

                $each->setValue(json_encode($value));

            } elseif ($property->getName() == 'media_id' || ($property->getDataType()->getColumnType() == 'collection' && $property->getDataType()->getFormField() == 'file')) {
                if(is_array($value)) {
                    $advertisement->getAdvertisementPropertyValues()->remove($i);
                    continue;
                }

                if($value && is_object($value) && get_class($value) == $fileClassName) {
                    $imageType = $property->getName() == 'media_id' ? AdvertisementMediaService::ICON_TYPE_IMAGE : AdvertisementMediaService::MEDIUM_BANNER_IMAGE; 
                    
                    $media = $this->get('services.advertisement.media')->upload($value, $advertisement, $imageType);
                    $each->setValue($media->getId());

                    if($media) { // TODO - Temporary fixed for ads Image
                        $em = $this->getDoctrine()->getEntityManager();
                        $em->persist($each);                        
                    }
                }                
            }
        }
    }

    public function ajaxDeleteImageAction($advertisementPropertyValueId)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $advertisementValue = $em->getRepository('AdvertisementBundle:AdvertisementPropertyValue')->find($advertisementPropertyValueId);

        if($advertisementValue) {
            $advertisement = $advertisementValue->getAdvertisement();
            $advertisementDenormolized = $em->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')->find($advertisement->getId());
            
            if($advertisementValue->getAdvertisementPropertyName()->getName() == 'media_id') {
                $advertisementDenormolized->setMediaId(0);
                $em->remove($advertisementValue);
            }

            if($advertisementValue->getAdvertisementPropertyName()->getName() == 'highlight_featured_images') {
                $featuedImages = json_decode($advertisementDenormolized->getHighlightFeaturedImages(), true);
                foreach($featuedImages as $i => $each) {
                    if($each['id'] == $advertisementValue->getValue())
                        unset($featuedImages[$i]);
                }
                $advertisementDenormolized->setHighlightFeaturedImages(json_encode($featuedImages));
                $em->remove($advertisementValue);

            } elseif($advertisementValue->getAdvertisementPropertyName()->getName() == 'highlights') {
                $highlights = json_decode($advertisementValue->getValue(), true);
                $index = $this->getRequest()->get('index');
                $highlights[$index]['icon'] = null;

                $advertisementValue->setValue(json_encode($highlights));
                $advertisementDenormolized->setHighlights($advertisementValue->getValue());
                $em->persist($advertisementValue);
            }

            $em->persist($advertisementDenormolized);
            $em->flush();
            $result = true;

            // Invalidate Advertisement cache
            $this->invalidateAdsCache($advertisementDenormolized);
        }

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
    }

    public function updateStatusAction()
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();

        $advertisementId = $this->getRequest()->request->get('advertisementId');
        $advertisement = $em->getRepository('AdvertisementBundle:Advertisement')->find($advertisementId);

        if ($advertisement) {
            $status = $this->getRequest()->request->get('status');
            $advertisement->setStatus($status);
            $em->persist($advertisement);
            $em->flush();

            // Invalidate Advertisement cache
            $this->invalidateAdsCache($advertisement);


            // dispatch event
            //$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_CITY, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_CITY, $city));

            $result = true;
        }
    
        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
    
        return $response;
    }

    private function mediaObjectToArray(Media $media)
    {
        $mediaArray = null;

        if($media->getId()) {
            
            if(!$this->advertisement) {
                $this->advertisement = new Advertisement();
                $this->advertisement->setInstitution($this->institution);
            }

            $mediaArray = array(
                'id' => $media->getId(),
                'uuid' => $media->getUuid(),
                'name' => $media->getName(),
                'caption' => $media->getCaption()
            );            
        }

        return $mediaArray;
    }

    /**
     * 
     * @param Advertisement or AdvertisementDenormalizedProperty $advertisement
     */
    private function invalidateAdsCache($advertisement)
    {
        $advertisementType = $advertisement->getAdvertisementType()->getId();        
        $memcacheKey = FrontendMemcacheKeysHelper::getAdvertisementKeyByType($advertisementType);

        if(!$memcacheKey) {

            $denormalizedAd = ($advertisement instanceof AdvertisementDenormalizedProperty)
                ? $advertisement
                : $this->get('services.advertisement')->getDenomralizedPropertyById($advertisement->getId());

            switch($advertisementType)
            {
                case AdvertisementTypes::SEARCH_RESULTS_SPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsSpecializationFeaturedAdsKey($denormalizedAd->getSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_SUBSPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsSubSpecializationFeaturedAdsKey($denormalizedAd->getSubSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_TREATMENT_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsTreatmentFeaturedAdsKey($denormalizedAd->getTreatmentId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_CITY_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCityFeaturedAdsKey($denormalizedAd->getCityId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_CITY_SPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCitySpecializationFeaturedAdsKey($denormalizedAd->getCityId(), $denormalizedAd->getSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_CITY_SUBSPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCitySubSpecializationFeaturedAdsKey($denormalizedAd->getCityId(), $denormalizedAd->getSubSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_CITY_TREATMENT_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCityTreatmentFeaturedAdsKey($denormalizedAd->getCityId(), $denormalizedAd->getTreatmentId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_COUNTRY_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountryFeaturedAdsKey($denormalizedAd->getCountryId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_COUNTRY_SPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountrySpecializationFeaturedAdsKey($denormalizedAd->getCountryId(), $denormalizedAd->getSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_COUNTRY_SUBSPECIALIZATION_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountrySubSpecializationFeaturedAdsKey($denormalizedAd->getCountryId(), $denormalizedAd->getSubSpecializationId());
                    break;
                case AdvertisementTypes::SEARCH_RESULTS_COUNTRY_TREATMENT_FEATURE :
                    $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountryTreatmentFeaturedAdsKey($denormalizedAd->getCountryId(), $denormalizedAd->getTreatmentId());
                    break;
            }            
        }

        $this->get('services.memcache')->delete($memcacheKey);
    }
}