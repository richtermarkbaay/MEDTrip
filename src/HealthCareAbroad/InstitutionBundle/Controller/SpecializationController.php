<?php
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\PagerBundle\Adapter\ArrayAdapter;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionSpecialization;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionSpecializationFormType;
use HealthCareAbroad\InstitutionBundle\Event\InstitutionBundleEvents;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * Controller for Institution Specialization
 *
 *
 * TODO: these business rules should be moved to a service class
 *
 * A newly added Specialization has status DRAFT and will have this status until
 * at least one procedure type is added to it, after which the status will change
 * to PENDING. A Specialization with status PENDING should have at least one media
 * attached to it.
 *
 * Note: this spec needs to be verified with Hazel.
 *
 */
class SpecializationController extends InstitutionAwareController
{
    /**
     * Displays a list of of ACTIVE/APPROVED institution specializations by default.
     * Can also display a list of DRAFT, PENDING, and EXPIRED specializations.
     *
     * Uses the ListFilterBeforeController to get the filtered list and the pager.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $institutionRepository = $this->getDoctrine()->getRepository('InstitutionBundle:Institution');

        return $this->render('InstitutionBundle:Specialization:index.html.twig', array(
            'institutionSpecializations' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    /**
     * Displays form for editing an institution specialization
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     */
    public function editAction(Request $request)
    {
        $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($request->get('isId', 0));

        if (!$institutionSpecialization) {
            throw $this->createNotFoundException("Invalid institution specialization.");
        }
        $form = $this->createForm(new InstitutionSpecializationFormType(), $institutionSpecialization);

        return $this->render('InstitutionBundle:Specialization:edit.html.twig', array(
            'institutionSpecialization' => $institutionSpecialization,
            'form' => $form->createView(),
        ));
    }

    /**
     * Saves an institution specialization
     *
     * Dispatches an event upon successful save.
     *
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_SPECIALIZATION')")
     *
     */
    public function saveAction(Request $request)
    {
        if (!$request->isMethod('POST')) {
            return $this->_errorResponse("POST is the only allowed method", 405);
        }

        if ($isId= $request->get('isId', 0)) {
            $institutionSpecialization = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionSpecialization')->find($isId);
            if (!$institutionSpecialization) {
                throw $this->createNotFoundException("Invalid institution specialization.");
            }
        }
        else {
            $institutionSpecialization = new InstitutionSpecialization();
            $institutionSpecialization->setInstitution($this->institution);
        }
        $isNew = $institutionSpecialization->getId() == 0;
        $form = $this->createForm(new InstitutionSpecializationType(), $institutionSpecialization);
        $form->bind($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionSpecialization);
            $em->flush();

            $this->dispatchEvent(
                $isNew ? InstitutionBundleEvents::ON_ADD_INSTITUTION_SPECIALIZATION : InstitutionBundleEvents::ON_EDIT_INSTITUTION_SPECIALIZATION,
                $institutionSpecialization
            );

            $request->getSession()->setFlash('success', "Successfully ".($isNew?'added':'updated')." {$institutionSpecialization->getSpecialization()->getName()} specialization.");

            return $this->redirect($this->generateUrl('institution_specialization_edit', array('isId' => $institutionSpecialization->getId())));
        }
        else {

            return $this->render($isNew ? 'InstitutionBundle:Specialization:add.html.twig': 'InstitutionBundle:Specialization:edit.html.twig', array(
                'form' => $form->createView(),
                'isNew' => $isNew,
                'institutionSpecialization' => $institutionSpecialization
            ));
        }
    }

    /**
     * Convenience function for dispatching events for logs
     */
    private function dispatchEvent($eventName, $loggedEntity, $optionalArguments = array())
    {
        $optionalArguments['institutionId'] = $this->institution->getId();
        $event = $this->get('events.factory')->create($eventName, $loggedEntity, $optionalArguments);
        $this->get('event_dispatcher')->dispatch($eventName, $event);
    }

    private function _errorResponse($message, $code=500)
    {
        return new Response($message, $code);
    }

}