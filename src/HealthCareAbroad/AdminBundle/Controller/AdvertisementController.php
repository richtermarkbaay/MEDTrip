<?php
/**
 * 
 * @author Adelbert D. Silla
 * @author Allejo Chris G. Velarde
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;

use HealthCareAbroad\AdvertisementBundle\Entity\Advertisement;
use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;

use Symfony\Component\HttpFoundation\Request;
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

        if ($advertisementId = $this->getRequest()->get('advertisementId', 0)) {
            $this->advertisement = $this->getDoctrine()->getRepository('AdvertisementBundle:Advertisement')->find($advertisementId);

            if (!$this->advertisement) {
                throw $this->createNotFoundException("Invalid advertisement.");
            }
        }

        if ($institutionId = $this->getRequest()->get('institutionId', $ad['institution'])) {
            //$this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($institutionId);

            $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e')->from('InstitutionBundle:Institution', 'a')
               ->leftJoin('a.institutionMedicalCenters', 'b')
               ->leftJoin('b.institutionSpecializations', 'c')
               ->leftJoin('c.specialization', 'd')
               ->leftJoin('c.treatments', 'e')
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

            $this->get('services.advertisement')->save($advertisement);

            $request->getSession()->setFlash("success", "Successfully created advertisement. You may now generate invoice.");

            return $this->redirect($this->generateUrl('admin_advertisement_index'));
        }

        return $this->render('AdminBundle:Advertisement:form.html.twig', array(
            'formAction' => $formAction,
            'form' => $form->createView()
        ));
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
}