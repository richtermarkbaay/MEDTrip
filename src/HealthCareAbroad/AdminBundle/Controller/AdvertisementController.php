<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Controller;

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
    
    public function preExecute()
    {
        $this->factory = $this->get('services.advertisement.factory');
        
        if ($advertisementId = $this->getRequest()->get('advertisementId', 0)) {
            $this->advertisement = $this->factory->findById($advertisementId);
            
            if (!$this->advertisement) {
                throw $this->createNotFoundException("Invalid advertisement.");
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
        $advertisementTypeId = $request->get('advertisementTypes', 0);

        if ($advertisementTypeId == ListFilter::FILTER_KEY_ALL) {

        	$advertisementTypeId = 0;
        }
    
        $params = array('advertisementTypeId' => $advertisementTypeId,'advertisements' => $this->filteredResult, 'pager' => $this->pager);

        return $this->render('AdminBundle:Advertisement:index.html.twig', $params);
        
    }
    
    /**
     * This is the first step when adding an advertisement
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     */
    public function addBasicDetailAction(Request $request)
    {
        $form = $this->createForm(new AdvertisementFormType(), null, 
            array(
                AdvertisementFormType::OPTION_IS_NEW => true, 
                AdvertisementFormType::OPTION_FORCED_HIDDEN_FIELDS => array(AdvertisementFormType::FIELD_OBJECT, AdvertisementFormType::FIELD_TITLE, AdvertisementFormType::FIELD_DESCRIPTION
        )));
        
        if ($request->isMethod('POST')) {
            
            $form->bind($request);
                
            // set session data for this draft advertisement
            $data = array(
                AdvertisementFormType::FIELD_ADVERTISEMENT_TYPE => $form->get(AdvertisementFormType::FIELD_ADVERTISEMENT_TYPE)->getData(),
                AdvertisementFormType::FIELD_INSTITUTION => $form->get(AdvertisementFormType::FIELD_INSTITUTION)->getData()->getId()
            );
            $draftAdvertisements = $request->getSession()->get('draftAdvertisements', array());
            $uid = \uniqid();
            $draftAdvertisements[$uid] = $data;
            // update draftAdvertisements session data
            $request->getSession()->set('draftAdvertisements', $draftAdvertisements);
            
            return $this->redirect($this->generateUrl('admin_advertisement_addSpecificDetail', array('uid'=>$uid)));
        }
        
        return $this->render('AdminBundle:Advertisement:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * This is the second step in creating an advertisement
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     */
    public function addSpecificDetailAction(Request $request)
    {
        $uid = $request->get('uid', null);
        $draftAdvertisements = $request->getSession()->get('draftAdvertisements', array());
        
        if (!\array_key_exists($uid, $draftAdvertisements)) {
            $request->getSession()->setFlash('notice', "No data was recovered from draft advertisement {$uid}. Please create a new one again.");
            
            return $this->redirect($this->generateUrl('admin_advertisement_addBasicDetail'));
        }
        $draftData = $draftAdvertisements[$uid];
        $institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($draftData[AdvertisementFormType::FIELD_INSTITUTION]);
        $advertisement = $this->get('services.advertisement.factory')->createInstanceByType($draftData[AdvertisementFormType::FIELD_ADVERTISEMENT_TYPE]);
        $advertisement->setInstitution($institution);
        
        $form = $this->createForm($this->factory->createAdvertisementTypeSpecificForm($advertisement), $advertisement);
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                if($request->files->get('advertisement')) {
                    $adsArray = $request->files->get('advertisement');
                    $media = $adsArray['media'];
                }
                try {
                    
                    $advertisement = $this->factory->save($form->getData());
                    if ($media) {
                        $media = $this->get('services.media')->addMedia($media, $institution->getId());
                        $media = $this->get('services.media')->addAdvertisementMedia($advertisement, $media);
                    }    
                    // dispatch event
                    $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_ADD_ADVERTISEMENT, $this->get('events.factory')->create(AdminBundleEvents::ON_ADD_ADVERTISEMENT, $advertisement));
                    $redirectUrl = $this->generateUrl('admin_advertisement_addInvoice', array('advertisementId' => $advertisement->getId()));
                    
                    $request->getSession()->setFlash("success", "Successfully created advertisement. You may now generate invoice.");
                }
                catch (\Exception $e) {
                    $request->getSession()->setFlash("error", "Failed to save advertisement due to unexpected error.");
                    $redirectUrl = $this->generateUrl("admin_advertisement_index");
                }
                // unset this draft in session
                unset($draftAdvertisements[$uid]); 
                $request->getSession()->get('draftAdvertisements', $draftAdvertisements);
                
                return $this->redirect($redirectUrl);
            }
        }
        
        return $this->render('AdminBundle:Advertisement:add.html.twig', array(
            'form' => $form->createView(),
            'selectedStep' => 'step2',
            'nextButtonLabel' => 'Save and Go to Invoice',
            'formAction' => $this->generateUrl('admin_advertisement_addSpecificDetail', array('uid' => $uid))
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
    
    /**
     * This is the edit advertisement page
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $form = $this->createForm($this->factory->createAdvertisementTypeSpecificForm($this->advertisement), $this->advertisement);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                try {
                    $advertisement = $this->factory->save($form->getData());
                
                    // dispatch event
                    $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_ADVERTISEMENT, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_ADVERTISEMENT, $advertisement));
                    
                    $request->getSession()->setFlash("success", "Successfully updated advertisement.");
                }
                catch (\Exception $e) {
                    $request->getSession()->setFlash("error", "Failed to updated advertisement due to unexpected error.");
                }
                
                return $this->redirect($this->generateUrl("admin_advertisement_index"));
            }
        }
        
        return $this->render('AdminBundle:Advertisement:edit.html.twig', array(
            'form' => $form->createView(),
            'advertisement' => $this->advertisement
        ));
    }
}