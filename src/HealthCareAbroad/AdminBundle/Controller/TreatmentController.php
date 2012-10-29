<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use HealthCareAbroad\HelperBundle\Services\Filters\ListFilter;
use HealthCareAbroad\TreatmentBundle\Entity\Treatment;
use HealthCareAbroad\TreatmentBundle\Form\TreatmentFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class TreatmentController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_TREATMENT')")
     */
    public function indexAction(Request $request)
    {
        $subSpecializationId = $request->get('subSpecialization', 0);
        if ($subSpecializationId == ListFilter::FILTER_KEY_ALL) {
            $subSpecializationId = 0;
        }

        $params = array('subSpecializationId' => $subSpecializationId,'treatments' => $this->filteredResult, 'pager' => $this->pager);

        return $this->render('AdminBundle:Treatment:index.html.twig', $params);
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_TREATMENT')")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction()
    {
        $params = $formActionParams = array();
        $treatment = new Treatment();

        if($subSpecializationId = $this->getRequest()->get('subSpecializationId', 0)) {
            $subSpecialization = $this->getDoctrine()->getRepository('TreatmentBundle:SubSpecialization')->find($subSpecializationId);

            if(!$subSpecialization) {
                throw $this->createNotFoundException("Invalid SubSpecialization.");
            }

            $treatment->setSubSpecialization($subSpecialization);

            $params['isAddFromSpecificType'] = true;
            $formActionParams['subSpecializationId'] = $subSpecializationId;
        }

        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_treatment_create', $formActionParams);
        return $this->render('AdminBundle:Treatment:form.html.twig', $params);
    }
    
    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_TREATMENT')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction($id)
    {
        if($id) {
            $treatment = $this->get('services.treatmentBundle')->getTreatment($id);
            if(!$treatment) {
                throw $this->createNotFoundException("Invalid Treatment.");
            }
        }

        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);

        $params['form'] = $form->createView();
        $params['formAction'] = $this->generateUrl('admin_treatment_update', array('id' => $treatment->getId()));
        $params['hasInstitutionTreatments'] = false; // TODO SELECT InstitutionTreatments count(*)
        return $this->render('AdminBundle:Treatment:form.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_TREATMENT')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        if($id) {
            $treatment = $em->getRepository('TreatmentBundle:Treatment')->find($id);

            if(!$treatment) throw $this->createNotFoundException("Invalid Treatment.");
            
        } else $treatment = new Treatment();


        $treatmentForm = new TreatmentFormType();
        $treatmentForm->setDoctrine($this->getDoctrine());

        $form = $this->createForm($treatmentForm, $treatment);
        $form->bind($request);

        if ($form->isValid()) {
            $em->persist($treatment);
            $em->flush($treatment);

            // dispatch event
            $eventName = $id ? AdminBundleEvents::ON_EDIT_TREATMENT : AdminBundleEvents::ON_ADD_TREATMENT;
            $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $treatment));
            
            $request->getSession()->setFlash('success', 'Treatment has been saved!');
            
            if($request->get('submit') == 'Save')
                return $this->redirect($this->generateUrl('admin_treatment_edit', array('id' => $treatment->getId())));
            else {
                $treatmentId = $request->get('treatmentId');
                $addParams = $treatmentId ? array('treatmentId' => $treatmentId) : array();

                return $this->redirect($this->generateUrl('admin_treatment_add', $addParams));
            }

        } else {
            $treatmentId = $request->get('treatmentId');
            $params = $formCreateParams = array();

            if($treatmentId) {
                $params['isAddFromSpecificType'] = true;
                $formCreateParams['treatmentId'] = $treatmentId;
            }

            if(!$treatment->getId()) {
                $formAction = $this->generateUrl('admin_treatment_create', $formCreateParams);
            } else {
                $formAction = $this->generateUrl('admin_treatment_update', array('id' => $treatment->getId()));
            }

            $params['form'] = $form->createView();
            $params['formAction'] = $formAction;
            $params['hasInstitutionTreatments'] = false; // TODO SELECT InstitutionTreatments count(*)
            
            return $this->render('AdminBundle:Treatment:form.html.twig', $params);
        }
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_TREATMENT')")
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $treatment = $this->get('services.treatmentBundle')->getTreatment($id);

        if($treatment) {
            $em = $this->getDoctrine()->getEntityManager();
            $status = $treatment->getStatus() == Treatment::STATUS_ACTIVE 
                    ? Treatment::STATUS_INACTIVE
                    : Treatment::STATUS_ACTIVE;

            $treatment->setStatus($status);
            $em->persist($treatment);
            $em->flush($treatment);
            
            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_TREATMENT, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_TREATMENT, $treatment));
            
            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}