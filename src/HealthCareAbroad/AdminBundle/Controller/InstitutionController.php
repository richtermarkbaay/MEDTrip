<?php

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentProcedureFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentFormType;

use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InstitutionController extends Controller
{    
    protected $institution;
    protected $institutionMedicalCenter;
    protected $institutionTreatment;
    protected $institutionMedicalProcedure;

    function preExecute() 
    {
        $request = $this->getRequest();
        // Check Institution
        if ($request->get('institutionId')) {
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->find($request->get('institutionId'));
            
            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid Institution');                
            }
        }

        // Check InstitutionMedicalCenter        
        if ($request->get('imcId')) {
            $this->institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

            if(!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid InstitutionMedicalCenter.');
            }
        }

        // Check InstitutionTreatment
        if ($request->get('imptId')) {
            $this->institutionTreatment = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatment')->find($request->get('imptId'));

            if(!$this->institutionTreatment) {
                throw $this->createNotFoundException('Invalid InstitutionTreatment.');
            }
        }
        
        // Check InstitutionTreatmentProcedure
        if ($request->get('impId')) {
            $this->institutionMedicalProcedure = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionTreatmentProcedure')->find($request->get('impId'));
        
            if(!$this->institutionMedicalProcedure) {
                throw $this->createNotFoundException('Invalid InstitutionTreatment.');
            }
        }
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INSTITUTIONS')")
     */
    public function indexAction()
    {
        $params = array(
            'pager' => $this->pager,
            'institutions' => $this->filteredResult, 
            //'statusList' => InstitutionStatus::getStatusList(),
            'statusList' => InstitutionStatus::getBitValueLabels(),
            'updateStatusOptions' => InstitutionStatus::getUpdateStatusOptions()
        );

        return $this->render('AdminBundle:Institution:index.html.twig', $params);
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function viewAction(Request $request)
    {   
        return $this->render('AdminBundle:Institution:view.html.twig', array('institution' => $this->institution));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_INSTITUTION')")
     */
    public function updateStatusAction()
    {
        $request = $this->getRequest();

        if(!InstitutionStatus::isValid($request->get('status'))) {
            $request->getSession()->setFlash('error', 'Unable to update status. ' . $request->get('status') . ' is invalid status value!');

            return $this->redirect($this->generateUrl('admin_institution_index'));
        }

        $this->institution->setStatus(InstitutionStatus::getBitValue($request->get('status')));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institution);
        $em->flush($this->institution);

        // dispatch EDIT institution event
        $event = $this->get('events.factory')->create(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $this->institution);
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_EDIT_INSTITUTION, $event);

        $request->getSession()->setFlash('success', '"'.$this->institution->getName().'" has been updated!');

        return $this->redirect($this->generateUrl('admin_institution_index'));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function manageCentersAction()
    {
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionName' => $this->institution->getName(),
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(), 
            'institutionMedicalCenters' => $this->filteredResult,
            'pager' => $this->pager
        );

        return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function addMedicalCenterAction()
    {
        $this->institutionMedicalCenter = new InstitutionMedicalCenter();
        $this->institutionMedicalCenter->setInstitution($this->institution);

        $form = $this->createForm(new InstitutionMedicalCenterType(), $this->institutionMedicalCenter);

        $formAction = $this->generateUrl('admin_institution_medicalCenter_create', array('institutionId' => $this->institution->getId()));

        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'formAction' => $formAction,
            'form' => $form->createView()
        );

        return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);    
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editMedicalCenterAction()
    {
        $form = $this->createForm(new InstitutionMedicalCenterType(), $this->institutionMedicalCenter);

        $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());
        $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);

        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'centerStatusList' => InstitutionMedicalCenterStatus::getStatusList(),
            'updateCenterStatusOptions' => InstitutionMedicalCenterStatus::getUpdateStatusOptions(),
            'formAction' => $formAction,
            'form' => $form->createView()
        );

        return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
    }
    
    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function saveMedicalCenterAction()
    {
        $request = $this->getRequest();

        if(!$request->isMethod('POST')) {
            return new Response("Save requires POST method!", 405);
        }

        if(!$this->institutionMedicalCenter) {
            $this->institutionMedicalCenter = new InstitutionMedicalCenter;
            $this->institutionMedicalCenter->setInstitution($this->institution);
        }

        $form = $this->createForm(new InstitutionMedicalCenterType(), $this->institutionMedicalCenter);
        $form->bind($request);

        if($form->isValid()) {
            if(!$request->get('imcId'))
                $this->institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::INACTIVE);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionMedicalCenter);
            $em->flush($this->institutionMedicalCenter);

            // dispatch ADD or EDIT institutionMedicalCenter event
            $actionEvent = $request->get('imcId') ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER : InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER;
            $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $this->institution->getId()));
            $this->get('event_dispatcher')->dispatch($actionEvent, $event);

            $request->getSession()->setFlash('success', 'Medical center has been saved!');

            if($request->get('submit') == 'Save') {
                $routeParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());

                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', $routeParams));
            } else {            
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }

        } else {

            if($this->institutionMedicalCenter->getId()) {
                $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId());
                $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
            } else {
                $formActionParams = array('institutionId' => $this->institution->getId());
                $formAction = $this->generateUrl('admin_institution_medicalCenter_create', $formActionParams);
            }

            $params = array(
                'form' => $form->createView(),
                'institutionId' => $this->institution->getId(),
                'institutionMedicalCenter' => $this->institutionMedicalCenter,
                'formAction' => $formAction
            );

            return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
        }
    }


    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */ 
    public function updateMedicalCenterStatusAction()
    {
        $request = $this->getRequest();
        $status = $request->get('status');

        $redirectUrl = $this->generateUrl('admin_institution_manageCenters', array('institutionId' => $request->get('institutionId')));
        
        if(!InstitutionMedicalCenterStatus::isValid($status)) {
            $request->getSession()->setFlash('error', "Unable to update status. $status is invalid status value!");

            return $this->redirect($redirectUrl);
        }

        $this->institutionMedicalCenter->setStatus($status);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenter);
        $em->flush($this->institutionMedicalCenter);

        // dispatch EDIT institutionMedicalCenter event
        $actionEvent = InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenter, array('institutionId' => $this->institutionMedicalCenter->getInstitution()->getId()));
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);

        $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenter->getMedicalCenter()->getName().'" status has been updated!');

        return $this->redirect($redirectUrl);
    }


    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function addProcedureTypeAction()
    {    
        $institutionTreatment = new InstitutionTreatment();
        $institutionTreatment->setInstitutionMedicalCenter($this->institutionMedicalCenter);

        $form = $this->createForm(new InstitutionTreatmentFormType(), $institutionTreatment);

        return $this->render("AdminBundle:Institution:modalForm.treatment.html.twig", array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionTreatment' => $this->institutionTreatment,
            'form' => $form->createView(),
            'newProcedureType' => true
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editProcedureTypeAction()
    {
        $form = $this->createForm(new InstitutionTreatmentFormType(), $this->institutionTreatment);

        return $this->render("AdminBundle:Institution:modalForm.treatment.html.twig", array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionTreatment' => $this->institutionTreatment,
            'form' => $form->createView(),
            'newProcedureType' => false
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */    
    public function saveProcedureTypeAction()
    {
        $request = $this->getRequest();

        if (!$request->isMethod('POST')) {
            return new Response('Unsupported method', 405);
        }

        if(!$this->institutionTreatment) {
            $this->institutionTreatment = new InstitutionTreatment();
            $this->institutionTreatment->setInstitutionMedicalCenter($this->institutionMedicalCenter);            
        }

        $form = $this->createForm(new InstitutionTreatmentFormType(), $this->institutionTreatment);
        $form->bindRequest($request);

        if ($form->isValid()){
            $this->institutionTreatment->setStatus(InstitutionTreatment::STATUS_ACTIVE);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionTreatment);
            $em->flush($this->institutionTreatment);

            // dispatch EDIT institutionTreatment event
            $actionEvent = $request->get('imptId') 
                ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT 
                : InstitutionBundleEvents::ON_ADD_INSTITUTION_TREATMENT;
            $event = $this->get('events.factory')->create($actionEvent, $this->institutionTreatment);
            $this->get('event_dispatcher')->dispatch($actionEvent, $event);
            
            $request->getSession()->setFlash('success', 'Successfully saved institution procedure type.');

            $url = $this->generateUrl('admin_institution_medicalCenter_edit', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenter->getId(), 'imptId' => $this->institutionTreatment->getId()));

            $response = new Response(json_encode(array('redirect_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }

        return $this->render('AdminBundle:Institution:modalForm.treatment.html.twig', array(
            'institution' => $this->institution,
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionTreatment' => $this->institutionTreatment,
            'form' => $form->createView(),
            'newProcedureType' => !$request->get('imptId'),
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function addProcedureAction()
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();

        $institutionMedicalProcedure = new InstitutionTreatmentProcedure();
        $institutionMedicalProcedure->setInstitutionTreatment($this->institutionTreatment);
        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $institutionMedicalProcedure);

        $formActionParams = array(
            'institutionId' => $this->institution->getId(),
            'imcId' => $this->institutionMedicalCenter->getId(),
            'imptId' => $this->institutionTreatment->getId(),
        );

        $formAction = $this->generateUrl('admin_institution_treatmentProcedure_create', $formActionParams);
        
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionTreatment' => $this->institutionTreatment,
            'formAction' => $formAction,
            'isNew' => true,
            'form' => $form->createView()
        );

        return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editProcedureAction()
    {
        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $this->institutionMedicalProcedure);
        $formActionParams = array(
            'institutionId' => $this->institution->getId(),
            'imcId' => $this->institutionMedicalCenter->getId(),
            'imptId' => $this->institutionTreatment->getId(),
            'impId' => $this->institutionMedicalProcedure->getId()
        );

        $formAction = $this->generateUrl('admin_institution_treatmentProcedure_update', $formActionParams);

        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'institutionTreatment' => $this->institutionTreatment,
            'treatmentProcedureName' => $this->institutionMedicalProcedure->getTreatmentProcedure()->getName(),
            'formAction' => $formAction,
            'isNew' => false,
            'form' => $form->createView()
        );

        return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveProcedureAction()
    {
        $request = $this->getRequest();

        if (!$request->isMethod('POST')) {
            return new Response('Unsupported method', 405);
        }

        if (!$this->institutionMedicalProcedure) {
            $this->institutionMedicalProcedure = new InstitutionTreatmentProcedure();
            $this->institutionMedicalProcedure->setInstitutionTreatment($this->institutionTreatment);
        }

        $form = $this->createForm(new InstitutionTreatmentProcedureFormType(), $this->institutionMedicalProcedure);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionMedicalProcedure);
            $em->flush($this->institutionMedicalProcedure);
            
            // dispatch ADD or EDIT institutionMedicalProcedure event
            $actionEvent = $request->get('impId') 
                ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT_PROCEDURE 
                : InstitutionBundleEvents::ON_ADD_INSTITUTION_TREATMENT_PROCEDURE;
            $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalProcedure);
            $this->get('event_dispatcher')->dispatch($actionEvent, $event);
            
            $request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$this->institutionTreatment->getTreatment()->getName()}\" procedure type.");

            $params = array(
                 'institutionId' => $this->institution->getId(),
                 'imcId' => $this->institutionMedicalCenter->getId(),
                 'imptId' => $this->institutionTreatment->getId()
            );

            $url = $this->generateUrl('admin_institution_medicalCenter_edit', $params);

            $response = new Response(json_encode(array('redirect_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        } else {

            $formActionParams = array(
                'institutionId' => $this->institution->getId(),
                'imcId' => $this->institutionMedicalCenter->getId(),
                'imptId' => $this->institutionTreatment->getId(),
            );

            if(!$request->get('impId')) {
                $formAction = $this->generateUrl('admin_institution_treatmentProcedure_create', $formActionParams);
                $params['isNew'] = true;

            } else {
                $formActionParams['impId'] = $this->institutionMedicalProcedure->getId();
                $formAction = $this->generateUrl('admin_institution_treatmentProcedure_update', $formActionParams);

                $params['isNew'] = true;
                $params['treatmentProcedureName'] = $this->institutionMedicalProcedure->getTreatmentProcedure()->getName();
            }

            $params['institutionId'] = $this->institution->getId();
            $params['institutionMedicalCenter'] = $this->institutionMedicalCenter;
            $params['institutionTreatment'] = $this->institutionTreatment;
            $params['formAction'] = $formAction;
            $params['form'] = $form->createView();
            
            return $this->render('AdminBundle:Institution:modalForm.procedure.html.twig', $params);
        }
    }

    /**
     * 
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function updateProcedureStatusAction()
    {
        $status = $this->institutionMedicalProcedure->getStatus() == InstitutionTreatmentProcedure::STATUS_ACTIVE
            ? InstitutionTreatmentProcedure::STATUS_INACTIVE
            : InstitutionTreatmentProcedure::STATUS_ACTIVE;

        $this->institutionMedicalProcedure->setStatus($status);
        
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalProcedure);
        $em->flush($this->institutionMedicalProcedure);
        
        // dispatch ADD or EDIT institutionMedicalProcedure event
        $actionEvent = InstitutionBundleEvents::ON_EDIT_INSTITUTION_TREATMENT_PROCEDURE;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalProcedure);
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);

        $response = new Response(json_encode(true));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}