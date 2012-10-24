<?php

namespace HealthCareAbroad\AdminBundle\Controller;


use HealthCareAbroad\InstitutionBundle\Form\institutionMedicalCenterGroupFormType;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Entity\TreatmentProcedure;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;
use HealthCareAbroad\InstitutionBundle\Entity\institutionMedicalCenterGroup;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatmentProcedure;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;
use HealthCareAbroad\InstitutionBundle\Entity\institutionMedicalCenterGroupStatus;

use HealthCareAbroad\InstitutionBundle\Form\institutionMedicalCenterGroupType;
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
    protected $institutionMedicalCenterGroup;
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

        // Check institutionMedicalCenterGroup        
        if ($request->get('imcId')) {
            $this->institutionMedicalCenterGroup = $this->getDoctrine()->getRepository('InstitutionBundle:institutionMedicalCenterGroup')->find($request->get('imcId'));

            if(!$this->institutionMedicalCenterGroup) {
                throw $this->createNotFoundException('Invalid institutionMedicalCenterGroup.');
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
        
        $this->service = $this->get('services.institution_medical_center_group');
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
        //var_dump($this->filteredResult[0]->getinstitutionMedicalCenterGroups());
        
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionName' => $this->institution->getName(),
            'centerStatusList' => institutionMedicalCenterGroupStatus::getStatusList(),
            'updateCenterStatusOptions' => institutionMedicalCenterGroupStatus::getUpdateStatusOptions(), 
            'institutionMedicalCenterGroups' => $this->filteredResult,
            'pager' => $this->pager
        );

        return $this->render('AdminBundle:Institution:manage_centers.html.twig', $params);
    }

    /**
     * This is the first step when creating a new institutionMedicalCenterGroup. Add details of a institutionMedicalCenterGroup
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addMedicalCenterDetailsAction(Request $request)
    {
        if (is_null($this->institutionMedicalCenterGroup)) {
            $this->institutionMedicalCenterGroup = new institutionMedicalCenterGroup();
            $this->institutionMedicalCenterGroup->setInstitution($this->institution);
        }
        else {
            // there is an imcgId in the Request, check if this is a draft
            if ($this->institutionMedicalCenterGroup && !$this->service->isDraft($this->institutionMedicalCenterGroup)) {
                return $this->_redirectIndexWithFlashMessage('Invalid draft medical center group', 'error');
            }
        }
        
        $form = $this->createForm(new institutionMedicalCenterGroupFormType(),$this->institutionMedicalCenterGroup);
        if ($request->isMethod('POST')) {
            $form->bind($request);
            
            if ($form->isValid()) {
                
                $this->institutionMedicalCenterGroup = $this->get('services.institutionMedicalCenterGroup')
                    ->saveAsDraft($form->getData());
                
                // TODO: fire event
                
                // redirect to step 2;
                return $this->redirect($this->generateUrl('institution_medicalCenterGroup_addSpecializations',array('imcgId' => $this->institutionMedicalCenterGroup->getId())));
            }
        }
        
        $params = array(
            'form' => $form->createView(),
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup
        );
        
        return $this->render('AdminBundle:Institution:form.medicalCenter.html.twig', $params);
    }

    /**
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function editMedicalCenterAction()
    {
        $form = $this->createForm(new institutionMedicalCenterGroupType(), $this->institutionMedicalCenterGroup);

        $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenterGroup->getId());
        $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);

        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
            'centerStatusList' => institutionMedicalCenterGroupStatus::getStatusList(),
            'updateCenterStatusOptions' => institutionMedicalCenterGroupStatus::getUpdateStatusOptions(),
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

        if(!$this->institutionMedicalCenterGroup) {
            $this->institutionMedicalCenterGroup = new institutionMedicalCenterGroup;
            $this->institutionMedicalCenterGroup->setInstitution($this->institution);
        }

        $form = $this->createForm(new institutionMedicalCenterGroupType(), $this->institutionMedicalCenterGroup);
        $form->bind($request);

        if($form->isValid()) {
            if(!$request->get('imcId'))
                $this->institutionMedicalCenterGroup->setStatus(institutionMedicalCenterGroupStatus::INACTIVE);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($this->institutionMedicalCenterGroup);
            $em->flush($this->institutionMedicalCenterGroup);

            // dispatch ADD or EDIT institutionMedicalCenterGroup event
            $actionEvent = $request->get('imcId') ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER : InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER;
            $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenterGroup, array('institutionId' => $this->institution->getId()));
            $this->get('event_dispatcher')->dispatch($actionEvent, $event);

            $request->getSession()->setFlash('success', 'Medical center has been saved!');

            if($request->get('submit') == 'Save') {
                $routeParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenterGroup->getId());

                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_edit', $routeParams));
            } else {            
                return $this->redirect($this->generateUrl('admin_institution_medicalCenter_add', array('institutionId' => $this->institution->getId())));
            }

        } else {

            if($this->institutionMedicalCenterGroup->getId()) {
                $formActionParams = array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenterGroup->getId());
                $formAction = $this->generateUrl('admin_institution_medicalCenter_update', $formActionParams);
            } else {
                $formActionParams = array('institutionId' => $this->institution->getId());
                $formAction = $this->generateUrl('admin_institution_medicalCenter_create', $formActionParams);
            }

            $params = array(
                'form' => $form->createView(),
                'institutionId' => $this->institution->getId(),
                'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
        
        if(!institutionMedicalCenterGroupStatus::isValid($status)) {
            $request->getSession()->setFlash('error', "Unable to update status. $status is invalid status value!");

            return $this->redirect($redirectUrl);
        }
        
        $this->institutionMedicalCenterGroup->setStatus($status);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($this->institutionMedicalCenterGroup);
        $em->flush($this->institutionMedicalCenterGroup);

        // dispatch EDIT institutionMedicalCenterGroup event
        $actionEvent = InstitutionBundleEvents::ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER;
        $event = $this->get('events.factory')->create($actionEvent, $this->institutionMedicalCenterGroup, array('institutionId' => $request->get('institutionId')));
        $this->get('event_dispatcher')->dispatch($actionEvent, $event);

        $request->getSession()->setFlash('success', '"'.$this->institutionMedicalCenterGroup->getName().'" status has been updated!');

        return $this->redirect($redirectUrl);
    }


    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_INSTITUTION')")
     */
    public function addProcedureTypeAction()
    {    
        $institutionTreatment = new InstitutionTreatment();
        $institutionTreatment->setinstitutionMedicalCenterGroup($this->institutionMedicalCenterGroup);

        $form = $this->createForm(new InstitutionTreatmentFormType(), $institutionTreatment);

        return $this->render("AdminBundle:Institution:modalForm.treatment.html.twig", array(
            'institution' => $this->institution,
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
            $this->institutionTreatment->setinstitutionMedicalCenterGroup($this->institutionMedicalCenterGroup);            
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
            
            $request->getSession()->setFlash('success', 'Successfully saved institution treatement.');

            $url = $this->generateUrl('admin_institution_medicalCenter_edit', array('institutionId' => $this->institution->getId(), 'imcId' => $this->institutionMedicalCenterGroup->getId(), 'imptId' => $this->institutionTreatment->getId()));

            $response = new Response(json_encode(array('redirect_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }

        return $this->render('AdminBundle:Institution:modalForm.treatment.html.twig', array(
            'institution' => $this->institution,
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
            'imcId' => $this->institutionMedicalCenterGroup->getId(),
            'imptId' => $this->institutionTreatment->getId(),
        );

        $formAction = $this->generateUrl('admin_institution_treatmentProcedure_create', $formActionParams);
        
        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
            'imcId' => $this->institutionMedicalCenterGroup->getId(),
            'imptId' => $this->institutionTreatment->getId(),
            'impId' => $this->institutionMedicalProcedure->getId()
        );

        $formAction = $this->generateUrl('admin_institution_treatmentProcedure_update', $formActionParams);

        $params = array(
            'institutionId' => $this->institution->getId(),
            'institutionMedicalCenterGroup' => $this->institutionMedicalCenterGroup,
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
            
            $request->getSession()->setFlash('success', "Successfully added a medical procedure to \"{$this->institutionTreatment->getTreatment()->getName()}\" treatment.");

            $params = array(
                 'institutionId' => $this->institution->getId(),
                 'imcId' => $this->institutionMedicalCenterGroup->getId(),
                 'imptId' => $this->institutionTreatment->getId()
            );

            $url = $this->generateUrl('admin_institution_medicalCenter_edit', $params);

            $response = new Response(json_encode(array('redirect_url' => $url)));
            $response->headers->set('Content-Type', 'application/json');

            return $response;

        } else {

            $formActionParams = array(
                'institutionId' => $this->institution->getId(),
                'imcId' => $this->institutionMedicalCenterGroup->getId(),
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
            $params['institutionMedicalCenterGroup'] = $this->institutionMedicalCenterGroup;
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