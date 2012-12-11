<?php
/**
 * 
 * @author Adelbert D. Silla
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementDenormalizedProperty;

use Doctrine\Common\Collections\ArrayCollection;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyValue;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorFormType;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementTypes;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementFactory;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;
use HealthCareAbroad\MediaBundle\Services\MediaService;
class AdvertisementController extends Controller
{
    /**
     * @var AdvertisementFactory
     */
    private $factory;
    
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
        $this->factory = $this->get('services.advertisement.factory');

        if ($advertisementId = $this->getRequest()->get('advertisementId', 0)) {
            $this->advertisement = $this->factory->findById($advertisementId);
            
            if (!$this->advertisement) {
                throw $this->createNotFoundException("Invalid advertisement.");
            }
        }

        if ($institutionId = $this->getRequest()->get('institutionId', 1)) {
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);

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
            'advertisementTypeId' => $advertisementTypeId,
            'advertisements' => $this->filteredResult,
            'pager' => $this->pager);

        return $this->render('AdminBundle:Advertisement:index.html.twig', $params);
        
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     */
    public function addAction(Request $request)
    {
        $advertisement = new Advertisement();
        $advertisementTypeId = $request->get('advertisementTypeId', 1);
        $advertisement->setInstitution($this->institution);

        if($advertisementTypeId) {
            $advertisementType = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementType')->find($advertisementTypeId);
            $advertisement->setAdvertisementType($advertisementType);
        }

        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new AdvertisementFormType($em), $advertisement);

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $this->generateUrl('admin_advertisement_create'),
            'form' => $form->createView()
        ));
    }

    /**
     * This is the edit advertisement page
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new AdvertisementFormType($em), $this->advertisement);

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $this->generateUrl('admin_advertisement_update', array('advertisementId' => $this->advertisement->getId())),
            'form' => $form->createView()
        ));
    }

    /**
     * This is the step when adding an advertisement
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
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
            
            $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($advertisementData['institution']);
            $advertisement->setInstitution($institution);

            $formAction = $this->generateUrl('admin_advertisement_create');
        } else {
            $advertisement = $this->advertisement;
            $formAction = $this->generateUrl('admin_advertisement_update', array('advertisementId'=>$advertisement->getId()));
        }
        
        $em = $this->getDoctrine()->getEntityManager();
        $form = $this->createForm(new AdvertisementFormType($em), $advertisement);

        $form->bind($request);

        if ($form->isValid()) {
            $this->saveMedia($request->files->get('advertisement'), $advertisement);

            if($advertisement->getId()) {
                foreach($advertisement->getAdvertisementPropertyValues()->getDeleteDiff() as $value) {
                    if($value->getAdvertisementPropertyName()->getName() != 'media_id') {
                        $em->remove($value);
                    }
                }
            }

            $em->persist($advertisement);
            $em->flush($advertisement);

            // Update Denormalized Advertisement Data
            $this->updateAdvertisementDenormalizedData($advertisement);

            $request->getSession()->setFlash("success", "Successfully created advertisement. You may now generate invoice.");

            return $this->redirect($this->generateUrl('admin_advertisement_index'));
        }

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $formAction,
            'form' => $form->createView()
        ));
    }

    private function saveMedia($fileBag, $advertisement)
    {
        if($fileBag && count($fileBag['advertisementPropertyValues'])) {
            $adValuesFile = array_shift($fileBag['advertisementPropertyValues']);

            if($adValuesFile['value']) {
                $media = $this->get('services.media')->uploadAds($adValuesFile['value'], $this->institution->getId());

                if($media && $media->getId()) {
                    foreach($advertisement->getAdvertisementPropertyValues() as $each) {
                        $property = $each->getAdvertisementPropertyName();
                        $config = json_decode($property->getPropertyConfig(), true);
        
                        if($config['type'] == 'file') {
                            $each->setValue($media->getId());

                            // TODO - Temporary fixed for ads Image
                            if($each->getId()) {
                                $em = $this->getDoctrine()->getEntityManager();
                                $em->persist($each);
                                $em->flush($each);
                            }

                            break;
                        }
                    }
                }
            }
        }
    }
    
    private function updateAdvertisementDenormalizedData(Advertisement $advertisement)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $denormalizedAdvertisement = $em->getRepository('AdvertisementBundle:AdvertisementDenormalizedProperty')->find($advertisement->getId());

        if(!$denormalizedAdvertisement) {
            $denormalizedAdvertisement = new AdvertisementDenormalizedProperty();
        }

        $advertisementMethods = get_class_methods($advertisement);        
        $propertyValues = $advertisement->getAdvertisementPropertyValues();

        $arrPropertyValues = array();
        foreach($propertyValues as $each) {
            $name = $each->getAdvertisementPropertyName()->getName();
            $name = str_replace('_', '', $name);

            if($each->getAdvertisementPropertyName()->getDataType()->getColumnType() == 'collection') {
                $arrPropertyValues[$name][] = (int)$each->getValue();
            } else {
                $arrPropertyValues[$name] = $each->getValue();                
            }
        }

        foreach(get_class_methods($denormalizedAdvertisement) as $method) {

            if(substr($method, 0, 3) == 'set') {
                $propertyName = substr($method, 3);
                
                $getMethod = 'get' . $propertyName;

                if(in_array($getMethod, $advertisementMethods)) {
                    $value = $advertisement->{$getMethod}();
                    $denormalizedAdvertisement->{$method}($value);                    
                } else {
                    if(isset($arrPropertyValues[strtolower($propertyName)])) {
                        $value = $arrPropertyValues[strtolower($propertyName)];
                        if(is_array($value)) {
                            $value = json_encode($value);
                        }
                    } 

                    else $value = '';
                }

                $denormalizedAdvertisement->{$method}($value);
            }
        }

        $em->persist($denormalizedAdvertisement);
        $em->flush($denormalizedAdvertisement);
    }    
    
    /**
     * This page will be the third step when creating a new advertisement.
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
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
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction(Request $request)
    {
        return $this->render('AdminBundle:Advertisement:preview.html.twig', array(
            'advertisement' => $this->advertisement
        ));
    }
}