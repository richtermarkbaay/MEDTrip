<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTreatment;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionMedicalCenterType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionTreatmentFormType;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for Institution Medical Center
 *
 *
 * TODO: these business rules should be moved to a service class
 *
 * A newly added MedicalCenter has status DRAFT and will have this status until
 * at least one procedure type is added to it, after which the status will change
 * to PENDING. A MedicalCenter with status PENDING should have at least one media
 * attached to it.
 *
 * Note: this spec needs to be verified with Hazel.
 *
 */
class MedicalCenterController extends InstitutionAwareController
{
    /**
     * Displays a list of of ACTIVE/APPROVED institution medical centers by default.
     * Can also display a list of DRAFT, PENDING, and EXPIRED medical centers.
     *
     * Uses the ListFilterBeforeController to get the filtered list and the pager.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution');

        return $this->render('InstitutionBundle:MedicalCenter:index.html.twig', array(
            'institutionMedicalCenters' => $this->filteredResult,
            'pager' => $this->pager
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
        $institutionTreatment = new InstitutionTreatment();
        $institutionTreatment->setInstitutionMedicalCenter($institutionMedicalCenter);

        $form = $this->createForm(new InstitutionTreatmentFormType(), $institutionTreatment);

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
     *
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

        $this->dispatchEvent(
            $hasDraft ? InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER : InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER,
            $institutionMedicalCenter
        );

        $request->getSession()->setFlash('success', "Successfully ".($hasDraft?'updated ':'added ')." {$institutionMedicalCenter->getMedicalCenter()->getName()} medical center.");

        return $this->redirect($this->generateUrl('institution_medicalCenter_addGallery', array('imcId' => $institutionMedicalCenter->getId())));
    }

    /**
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
        $institutionTreatment = new InstitutionTreatment();
        $institutionTreatment->setInstitutionMedicalCenter($institutionMedicalCenter);
        $institutionTreatment->setStatus(1);

        $form = $this->createForm(new InstitutionTreatmentFormType(), $institutionTreatment);
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
        $em->persist($institutionTreatment);
        $em->flush();

        $this->dispatchEvent(InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER, $institutionMedicalCenter);
        $this->dispatchEvent(InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_PROCEDURE_TYPE, $institutionTreatment);

        $request->getSession()->setFlash('success', "Successfully added {$institutionTreatment->getTreatment()->getName()} to {$institutionMedicalCenter->getMedicalCenter()->getName()}");

        if($request->get('submit') == 'Next') {
            $redirectUrl = $this->generateUrl('institution_medicalCenter_preview', array('imcId' => $request->get('imcId')));
        } else {
            $redirectUrl = $this->generateUrl('institution_medicalCenter_addProcedureTypes', array('imcId' => $request->get('imcId')));
        }

        return $this->redirect($redirectUrl);
    }

    /**
     * Deletes draft institution medical center. Related institution medical
     * procedure types are also deleted. Any associations to media files are
     * removed but the media itself is retained in the institution's media
     * library/gallery.
     *
     * Note: A institution medical with status DRAFT shouldn't have procedure
     * types. Will update code when this spec is confirmed.
     *
     * Dispatches an event upon success, passing in a proxy for deleted object.
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

        //$proxy = $this->getDoctrine()->getEntityManager()->getReference('HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter', $request->get('imcId'));
        $proxy = $this->getDoctrine()->getEntityManager()->getPartialReference('HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter', $request->get('imcId'));

        $this->dispatchEvent(InstitutionBundleEvents::ON_DELETE_INSTITUTION_MEDICAL_CENTER, $proxy);

        return $this->redirect($this->generateUrl('institution_medicalCenter_index'));
    }

    /**
     * Displays form for editing an institution medical center
     *
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
     * Saves an institution medical center
     *
     * Dispatches an event upon successful save.
     *
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

            $this->dispatchEvent(
                $isNew ? InstitutionBundleEvents::ON_ADD_INSTITUTION_MEDICAL_CENTER : InstitutionBundleEvents::ON_EDIT_INSTITUTION_MEDICAL_CENTER,
                $institutionMedicalCenter
            );

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

        $procedureTypes =  $repo->getAvailableTreatments($institutionMedicalCenter);
        foreach($procedureTypes as $each) {
            $data[] = array('id' => $each->getId(), 'name' => $each->getName());
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Convenience function for dispatching events for logs
     */
    private function dispatchEvent($eventName, $loggedEntity, $optionalArguments = array())
    {
        $event = $this->get('events.factory')->create($eventName, $loggedEntity, $optionalArguments);
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }

    private function _errorResponse($message, $code=500)
    {
        return new Response($message, $code);
    }

}