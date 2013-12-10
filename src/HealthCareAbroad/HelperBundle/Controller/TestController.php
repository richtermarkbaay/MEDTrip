<?php
namespace HealthCareAbroad\HelperBundle\Controller;

use HealthCareAbroad\HelperBundle\Form\FieldType\FancyCountryFieldType;

use HealthCareAbroad\HelperBundle\Form\ListType\GlobalCityListType;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\Validator\Constraints\NotBlank;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
class TestController extends Controller
{
    public function testLocationAction(Request $request)
    {
        $country = $this->getDoctrine()->getRepository('HelperBundle:Country')->find(206);
        $state = $this->getDoctrine()->getRepository('HelperBundle:State')->find(2363);
        $institution = new Institution();
        $institution->setName('test only '.time());
        $institution->setType(InstitutionTypes::MULTIPLE_CENTER);
//         $institution->setCountry($country);
//         $institution->setState($state);
        $validationGroups = array('editInstitutionInformation', 'Default');
        $validationGroups = array();
        
        $form = $this->createFormBuilder($institution, array('validation_groups' => $validationGroups))
            ->add('country', FancyCountryFieldType::NAME, array())
            ->add('city', GlobalCityListType::NAME, array('attr' => array('placeholder' => 'Select a city')))
            ->add('state', 'state_list',array('attr' => array('placeholder' => 'Select a state/province')))
        ->getForm();
        
        if ($request->isMethod('POST')){
            $form->bind($request);
            if ($form->isValid()){
                echo 'adi valid'; exit;
            }
            else {
                
                $errors = array();
                foreach ($form->getChildren() as $field){
                    foreach ($field->getErrors() as $err){
                        $errors[] = array(
                            'name' => $field->getName(),
                            'error' => $err->getMessage()
                        );
                    }
                }
                
                foreach ($form->getErrors() as $err){
                    $errors[] = array(
                        'name' => $form->getName(),
                        'error' => $err->getMessage()
                    );
                }
                var_dump($errors);
                echo 'adi invalid'; exit;
            }
        }
        
        return $this->render('HelperBundle:Test:testLocation.html.twig', array('form' => $form->createView()));
    }
    
    public function testContactDetailAction(Request $request)
    {
        $contactDetail = new ContactDetail();
        $contactDetail->setType(ContactDetailTypes::PHONE);
        
        $doctor = new Doctor();
        $doctor->setFirstName('Test Only');
        $doctor->setLastName('Doctor');
        $doctor->addContactDetail($contactDetail);
        $form = $this->createFormBuilder($doctor)
            ->add('contactDetails', 'collection', array('type' => 'simple_contact_detail', 'allow_add' => true))
            ->getForm();
        
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                
                $em = $this->getDoctrine()->getManager();
                $em->persist($contactDetail);
                //$em->flush();
                exit;
            }
            else {
                var_dump($form->getErrors()); exit;
            }
        }
        
        return $this->render('HelperBundle:Test:contactDetailTest.html.twig', array('form' => $form->createView()));
    }
    
    public function showFormAction()
    {
        $form = $this->createForm(new TrackerFormType());

        return  $this->render('StatisticsBundle:Tracker:form.html.twig', array('statsTrackerForm' => $form->createView()));
    }

    public function baselineAction(Request $request)
    {
        return $this->render('HelperBundle:Test:baseline.html.twig');
    }

    public function baselineResourcesAction(Request $request)
    {
        return $this->render('HelperBundle:Test:baselineResources.html.twig');
    }

    public function frontendBaseAction(Request $request)
    {
        return $this->render('HelperBundle:Test:frontendBase.html.twig');
    }

    public function saveClickthroughAction(Request $request)
    {
        $this->_throwUnsupportedMethodIfNotPost($request);

        $form = $this->createForm(new TrackerFormType());
        $form->bind($request);

        // invalid form. invalid token
        if (!$form->isValid()) {
            $this->_throwHttpException(400, 'Invalid form');
        }

        $clickthroughData = \trim($request->get('clickthroughData', ''));
        if ('' == $clickthroughData) {
            $this->_throwHttpException(400, 'no click through data');
        }
        $trackerFactory = $this->get('factory.statistics.dailyTracker');
        $decodedData = StatisticParameters::decodeParameters($clickthroughData);
        $tracker = $trackerFactory->getTrackerByType($decodedData->get(StatisticParameters::TYPE));
        if ($data = $tracker->createDataFromParameters($decodedData)) {
            $tracker->add($data);
            $tracker->batchSave();
        }

        return new Response('Clickthrough ok', 200);
    }

    public function ajaxTestAction(Request $request)
    {
        $request->getSession()->set('test', '123456');

        return $this->render('HelperBundle:Test:ajaxTest.html.twig');
    }

    public function ajaxSleepAction(Request $request)
    {
        $request->getSession()->get('test');

        $seconds = 10;

        sleep($seconds);

        $response = array('msg' => 'Slept for '.$seconds.' seconds');

        return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }

    public function ajaxCalledAfterwardsAction(Request $request)
    {
        $msg = 'No session';

        if ($request->get('hasSession')) {
            $request->getSession()->get('test');
            $msg = 'Manipulated session';
        }

        $response = array('msg' => $msg);

        return new Response(json_encode($response), 200, array('Content-Type'=>'application/json'));
    }
}