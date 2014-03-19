<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\TreatmentBundle\Entity\Specialization;
use HealthCareAbroad\TreatmentBundle\Form\SpecializationType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class SpecializationController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_SPECIALIZATIONS')")
     */
    public function indexAction()
    {
        $activeStatus = Specialization::STATUS_ACTIVE;
        
        $params = array('activeStatus' => Specialization::STATUS_ACTIVE, 'specializations' => $this->filteredResult, 'pager' => $this->pager);
        
        return $this->render('AdminBundle:Specialization:index.html.twig', $params);
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function manageAction(Request $request)
    {
        $specialization = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')
            ->find($request->get('id'));
        
        if (!$specialization){
        	throw $this->createNotFoundException('Invalid specialization');
        }
        
        return $this->render('AdminBundle:Specialization:manage.html.twig', array(
                'specialization' => $specialization
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function addAction()
    {
        $form = $this->createForm(new SpecializationType(), new Specialization());

        return $this->render('AdminBundle:Specialization:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_specialization_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function editAction($id)
    {
        $specialization = $this->getDoctrine()->getEntityManager()
                ->getRepository('TreatmentBundle:Specialization')->find($id);

        $form = $this->createForm('specialization_form', $specialization);

        return $this->render('AdminBundle:Specialization:form.html.twig', array(
            'id' => $id,
            'specialization' => $specialization,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_specialization_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $specialization = $id
                ? $em->getRepository('TreatmentBundle:Specialization')->find($id)
                : new Specialization();
        
        $form = $this->createForm(new SpecializationType(), $specialization);
        $form->bind($request);

        if ($form->isValid()) {

            if(($fileBag = $request->files->get('specialization_form')) && isset($fileBag['media'])) {
                $this->get('services.specialization.media')->uploadLogo($fileBag['media'], $specialization, false);                
            }

            $em->persist($specialization);
            $em->flush($specialization);

            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_SPECIALIZATION : AdminBundleEvents::ON_ADD_SPECIALIZATION;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $specialization));

            $request->getSession()->setFlash('success', 'Specialization saved!');

            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_specialization_edit', array('id' => $specialization->getId())));
            else
                return $this->redirect($this->generateUrl('admin_specialization_add'));
       }

        $formAction = $id
            ? $this->generateUrl('admin_specialization_update', array('id' => $id))
            : $this->generateUrl('admin_specialization_create');

        return $this->render('AdminBundle:Specialization:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $specialization = $em->getRepository('TreatmentBundle:Specialization')->find($id);

        if ($specialization) {
            $specialization->setStatus($specialization->getStatus() ? Specialization::STATUS_INACTIVE : Specialization::STATUS_ACTIVE);
            $em->persist($specialization);
            $em->flush($specialization);

            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_SPECIALIZATION, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_SPECIALIZATION, $specialization));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
    public function loadAvailableSubSpecializationsAction(Request $request)
    {
        $service = $this->get('services.treatment_bundle');
        $specialization = $service->getSpecialization($request->get('id', 0));
        if (!$specialization) {
            // find if treatment is passed
            $treatment = $service->getTreatment($request->get('treatmentId', 0));
            if ($treatment) {
                $specialization = $treatment->getSpecialization();
            }
            else {
                throw $this->createNotFoundException('No specialization given');
            }
        }
        
        $selectedSubSpecializationIds = $request->get('selectedSubSpecializationIds', array());
        $emptyValue = $request->get('empty_value', null);
        
        $subSpecializations = $service->getActiveSubSpecializationsBySpecialization($specialization);
        $output = array(
            'data' => array(),
            'html' => ''
        );
        
        if ($emptyValue) {
            $output['html'] .= "<option value='0' >{$emptyValue}</option>";
        }
        
        foreach ($subSpecializations as $each) {
            $isSelected = \in_array($each->getId(), $selectedSubSpecializationIds);
            $output['html'] .= "<option value='{$each->getId()}' ".($isSelected?"selected":"").">{$each->getName()}</option>";
            $output['data'][] = array(
                'id' => $each->getId(),
                'name' => $each->getName(),
                'selected' => $isSelected
            );
        }
        
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
    
//     private function saveMedia($fileBag, $specialization)
//     {
//         $media = null;

//         if($fileBag['media']) {
//             $media = $this->get('services.specialization.media')->upload($fileBag['media'], $specialization);
//         }

//         return $media;
//     }

}