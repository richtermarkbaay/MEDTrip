<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalProcedureType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalProcedureTypeFormType;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Purpose: Controller for Institution Medical Center (weh naa ko documentation)
 *
 *
 * TODO: these business rules should be moved to a service class
 *
 * A newly added MedicalCenter has status DRAFT and will have this status until
 * at least one procedure type is added to it. After which the status will change
 * to PENDING. A MedicalCenter with status PENDING should have at least one media
 * attached to it.
 *
 * Note: this spec needs to be verified with Hazel.
 *
 */
class MedicalCenterController extends InstitutionAwareController
{
    /**
     * Displays a list of of active institution medical centers.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution');

        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', array(
            'institutionMedicalCenters' => $this->filteredResult
        ));
    }

    /**
     * This is the FIRST STEP when adding/updating a draft center.
     * Displays form for adding or editing draft institution medical centers.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $imcId = $request->get('imcId', 0);

        if ($imcId === 0) {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
            $institutionMedicalCenter->setInstitution($this->institution);
        }
        else {
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
            if (!$institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center.");
            }
        }

        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);

        return $this->render('InstitutionBundle:MedicalCenter:add.html.twig', array(
            'form' => $form->createView(),
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'hasDraft' => InstitutionMedicalCenterStatus::DRAFT == $institutionMedicalCenter->getStatus()
        ));
    }

    /**
     * This is the SECOND STEP when adding/updating a draft center.
     * Displays page for adding or updating media for draft medical centers.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addGalleryAction(Request $request)
    {
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

        //TODO: load the media gallery tab via ajax
        $institutionId = $this->getRequest()->getSession()->get('institutionId');
        $institutionMedia = $this->get('services.media')->retrieveAllMedia($institutionId);

        return $this->render('InstitutionBundle:MedicalCenter:gallery.html.twig', array(
                        'institutionMedicalCenter' => $institutionMedicalCenter,
                        'institutionMedia' => $institutionMedia,
                        'institutionId' => $institutionId
        ));
    }

    /**
     * This is the THIRD STEP when adding/updating a draft center.
     * Displays page for adding or updating procedure types for draft medical centers.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addProcedureTypesAction(Request $request)
    {
        if (!$request->get('imcId', 0)) {
            throw $this->createNotFoundException("Invalid institution medical center id.");
        }

        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
        $institutionMedicalProcedureType->setInstitutionMedicalCenter($institutionMedicalCenter);

        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType);

        return $this->render('InstitutionBundle:MedicalCenter:addProcedureTypes.html.twig', array(
                        'institutionMedicalCenter' => $institutionMedicalCenter,
                        'form' => $form->createView()
        ));
    }

    /**
     * This is the FOURTH STEP when adding/updating a draft center.
     * Displays a preview of the "listings" page
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function previewAction(Request $request)
    {
        if (!$request->get('imcId', 0)) {
            throw $this->createNotFoundException("Invalid institution medical center id.");
        }

        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));

        return $this->render('InstitutionBundle:MedicalCenter:previewMedicalCenter.html.twig', array(
                        'institutionMedicalCenter' => $institutionMedicalCenter
        ));
    }

    /**
     * Saves a draft institution medical center. This is called after the
     * submitting the form in the FIRST STEP of adding/updating medical centers.
     * Dispatches an event upon successful save.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return mixed
     */
    public function saveDraftAction(Request $request)
    {
        $imcId = $request->get('imcId', 0);

        if ($imcId) {
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
            if (!$institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center.");
            }
        }
        else {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
            $institutionMedicalCenter->setInstitution($this->institution);
        }

        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        $form->bind($request);

        $hasDraft = InstitutionMedicalCenterStatus::DRAFT == $institutionMedicalCenter->getStatus();

        if (!$form->isValid()) {

            return $this->render('InstitutionBundle:MedicalCenter:add.html.twig', array(
                'form' => $form->createView(),
                'institutionMedicalCenter' => $institutionMedicalCenter,
                'hasDraft' => $hasDraft
            ));
        }

        $institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::DRAFT);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($institutionMedicalCenter);
        $em->flush();

        if ($hasDraft) {
            $this->dispatchEvent(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER);
        }
        else {
            $this->dispatchEvent(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER);
        }

        $request->getSession()->setFlash('success', "Successfully ".($hasDraft?'updated ':'added ')." {$institutionMedicalCenter->getMedicalCenter()->getName()} medical center.");

        return $this->redirect($this->generateUrl('institution_medicalCenter_addGallery', array('imcId' => $institutionMedicalCenter->getId())));
    }

    /**
     * TODO: refactor -> move to addProcedureTypeAction ?
     *
     * Adds a procedure type to a draft institution medical center. This is called
     * after the submitting the form in the THIRD STEP of adding/updating medical
     * centers.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function saveProcedureTypeAction(Request $request)
    {

        if (!$request->get('imcId', 0)) {
            throw $this->createNotFoundException("Invalid institution medical center id.");
        }

        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId'));
        $institutionMedicalProcedureType = new InstitutionMedicalProcedureType();
        $institutionMedicalProcedureType->setInstitutionMedicalCenter($institutionMedicalCenter);
        $institutionMedicalProcedureType->setStatus(1);

        $form = $this->createForm(new InstitutionMedicalProcedureTypeFormType(), $institutionMedicalProcedureType);
        $form->bind($request);

        if (!$form->isValid()) {
            return $this->render('InstitutionBundle:MedicalCenter:addProcedureTypes.html.twig', array(
                            'institutionMedicalCenter' => $institutionMedicalCenter,
                            'form' => $form->createView()
            ));
        }

        $institutionMedicalCenter->setStatus(InstitutionMedicalCenterStatus::PENDING);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($institutionMedicalCenter);
        $em->persist($institutionMedicalProcedureType);
        $em->flush();

        //TODO: dispatch event?

        $request->getSession()->setFlash('success', "Successfully added {$institutionMedicalProcedureType->getMedicalProcedureType()->getName()} to {$institutionMedicalCenter->getMedicalCenter()->getName()}");

        return $this->redirect($this->generateUrl('institution_medicalCenter_addProcedureTypes', array('imcId' => $request->get('imcId'))));
    }

    /**
     * Deletes draft institution medical center. Related institution medical
     * procedure types are also deleted. Any associations to media files are
     * removed but the media itself is retained in the institution's media
     * library/gallery.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     * @param Request $request
     */
    public function deleteDraftAction(Request $request)
    {
        if (!$request->get('imcId', 0)) {
            throw $this->createNotFoundException("Invalid institution medical center id.");
        }

        $this->get('services.institutionMedicalCenter')->deleteDraftInstitutionMedicalCenter($this->institution, $request->get('imcId'));

        return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function editAction(Request $request)
    {
        $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($request->get('imcId', 0));

        if (!$institutionMedicalCenter) {
            throw $this->createNotFoundException("Invalid institution medical center.");
        }
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        return $this->render('InstitutionBundle:MedicalCenter:edit.html.twig', array(
            'institutionMedicalCenter' => $institutionMedicalCenter,
            'form' => $form->createView(),
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     *
     */
    public function saveAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->_errorResponse("POST is the only allowed method", 405);
        }

        if ($imcId= $request->get('imcId', 0)) {
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->find($imcId);
            if (!$institutionMedicalCenter) {
                throw $this->createNotFoundException("Invalid institution medical center.");
            }
        }
        else {
            $institutionMedicalCenter = new InstitutionMedicalCenter();
            $institutionMedicalCenter->setInstitution($this->institution);
        }
        $isNew = $institutionMedicalCenter->getId() == 0;
        $form = $this->createForm(new InstitutionMedicalCenterType(), $institutionMedicalCenter);
        $form->bind($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionMedicalCenter);
            $em->flush();

            if ($isNew) {
                $this->dispatchEvent(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER);
            }
            else {
                $this->dispatchEvent(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER);
            }

            $request->getSession()->setFlash('success', "Successfully ".($isNew?'added':'updated')." {$institutionMedicalCenter->getMedicalCenter()->getName()} medical center.");

            return $this->redirect($this->generateUrl('institution_medicalCenter_edit', array('imcId' => $institutionMedicalCenter->getId())));
        }
        else {

            return $this->render($isNew ? 'InstitutionBundle:MedicalCenter:add.html.twig': 'InstitutionBundle:MedicalCenter:edit.html.twig', array(
                'form' => $form->createView(),
                'isNew' => $isNew,
                'institutionMedicalCenter' => $institutionMedicalCenter
            ));
        }
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_PROCEDURE_TYPES')")
     */
    function loadProcedureTypesAction(Request $request)
    {
        $data = array();
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        $institutionMedicalCenter = $repo->findOneBy(array('institution' => $this->institution->getId(), 'medicalCenter' => $request->get('medical_center_id')));

        if (!$institutionMedicalCenter) {
            throw $this->createNotFoundException('No InstitutionMedicalCenter found.');
        }

        $procedureTypes =  $repo->getAvailableMedicalProcedureTypes($institutionMedicalCenter);
        foreach($procedureTypes as $each) {
            $data[] = array('id' => $each->getId(), 'name' => $each->getName());
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Convenience function for dispatching events related to medical centers
     *
     * @param InstitutionMedicalCenter $institutionMedicalCenter
     * @param boolean $isNewObject
     */
    private function dispatchEvent($eventName, $dependentEntities = array())
    {
        $event = $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION, $dependentEntities);
        $this->get('event_dispatcher')->dispatch($eventName, $event);

        //sample code
        /*
        $this->get('event_dispatcher')->dispatch(InstitutionBundleEvents::ON_ADD_INSTITUTION,
            $this->get('events.factory')->create(InstitutionBundleEvents::ON_ADD_INSTITUTION, array('institution' => $institution, 'institutionUser' => $user)
        ));
        */
    }

    private function _errorResponse($message, $code=500)
    {
        return new Response($message, $code);
    }

}