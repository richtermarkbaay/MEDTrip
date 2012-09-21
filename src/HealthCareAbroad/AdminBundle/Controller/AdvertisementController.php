<?php
/**
 * 
 * @author Allejo Chris G. Velarde
 *
 */
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementFormType;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertisementController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('AdminBundle:Advertisement:index.html.twig');
    }
    
    /**
     * This is the first step when adding an advertisement
     * 
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
            
            if ($form->isValid()) {
                
                //$advertisement = $this->get('services.advertisement.factory')->createInstanceByType($form->get(AdvertisementFormType::FIELD_ADVERTISEMENT_TYPE)->getData());
                
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
        }
        
        return $this->render('AdminBundle:Advertisement:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    /**
     * This is the second step in creating an advertisement
     * 
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
        
        $form = $this->createForm(new AdvertisementFormType(), null,
            array(
                AdvertisementFormType::OPTION_IS_NEW => true,
                AdvertisementFormType::OPTION_FORCED_HIDDEN_FIELDS => array(AdvertisementFormType::FIELD_ADVERTISEMENT_TYPE, AdvertisementFormType::FIELD_INSTITUTION
        )));
        
        if ($request->isMethod('POST')) {
            
        }
        
        return $this->render('AdminBundle:Advertisement:add.html.twig', array(
            'form' => $form->createView(),
            'selectedStep' => 'step2',
            'nextButtonLabel' => 'Save and Go to Invoice'
        ));
    }
}