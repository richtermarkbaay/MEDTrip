<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\Affiliation;
use HealthCareAbroad\HelperBundle\Form\AffiliationFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AffiliationController extends Controller
{

    public function indexAction()
    {
        return $this->render('AdminBundle:Affiliation:index.html.twig', array(
            'affiliations' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    public function addAction()
    {
        $form = $this->createForm(New AffiliationFormType(), new Affiliation());

        return $this->render('AdminBundle:Affiliation:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_affiliation_create')
        ));
    }

    public function editAction($id)
    {
        $affiliation = $this->getDoctrine()->getEntityManager()
                ->getRepository('HelperBundle:Affiliation')->find($id);

        $form = $this->createForm(New AffiliationFormType(), $affiliation);

        return $this->render('AdminBundle:Affiliation:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_affiliation_update', array('id' => $id))
        ));
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $affiliation = $id ? $em->getRepository('HelperBundle:Affiliation')->find($id) : new Affiliation();

        $form = $this->createForm(New AffiliationFormType(), $affiliation);
           $form->bind($request);

           if ($form->isValid()) {
               $em->persist($affiliation);
               $em->flush($affiliation);

               // dispatch event
               $eventName = $id ? AdminBundleEvents::ON_EDIT_AFFILIATION : AdminBundleEvents::ON_ADD_AFFILIATION;
               $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $affiliation));

               $request->getSession()->setFlash('success', 'Affiliation has been saved!');
               return $this->redirect($this->generateUrl('admin_affiliation_index'));
        }

        $formAction = $id
            ? $this->generateUrl('admin_affiliation_update', array('id' => $id))
            : $this->generateUrl('admin_affiliation_create');

        return $this->render('AdminBundle:Affiliation:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction
        ));
    }

    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $affiliation = $em->getRepository('HelperBundle:Affiliation')->find($id);

        if ($affiliation) {
            $affiliation->setStatus($affiliation->getStatus() ? 0 : 1);
            $em->persist($affiliation);
            $em->flush($affiliation);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_AFFILIATION, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_AFFILIATION, $affiliation));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}