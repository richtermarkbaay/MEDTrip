<?php
/**
 * @author Chaztine Blance
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\HelperBundle\Entity\GlobalAwardTypes;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\HelperBundle\Entity\GlobalAward;
use HealthCareAbroad\HelperBundle\Form\GlobalAwardFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class GlobalAwardController extends Controller
{

    public function indexAction()
    {
        return $this->render('AdminBundle:GlobalAward:index.html.twig', array(
            'global_awards' => $this->filteredResult,
            'pager' => $this->pager,
            'types' => GlobalAwardTypes::getTypes()
        ));
    }

    public function addAction()
    {
        $form = $this->createForm(New GlobalAwardFormType(), new GlobalAward());

        return $this->render('AdminBundle:GlobalAward:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_global_award_create')
        ));
    }

    public function editAction($id)
    {
        $global_award = $this->getDoctrine()->getEntityManager()
                ->getRepository('HelperBundle:GlobalAward')->find($id);

        $form = $this->createForm(New GlobalAwardFormType(), $global_award);

        return $this->render('AdminBundle:GlobalAward:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_global_award_update', array('id' => $id))
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

        $global_award = $id ? $em->getRepository('HelperBundle:GlobalAward')->find($id) : new GlobalAward();
        
        $form = $this->createForm(New GlobalAwardFormType(), $global_award);
           $form->bind($request);

           if ($form->isValid()) {
               if(!$id && $award = $em->getRepository('HelperBundle:GlobalAward')->findOneBy(array('name' => $global_award->getName(), 'type' => $global_award->getType(), 'awardingBody' => $global_award->getAwardingBody()))) {
                   $request->getSession()->setFlash("failed", "Property value {$award->getName()} already exists!");
               }
               else {
                   $em->persist($global_award);
                   $em->flush($global_award);
                   
                   $request->getSession()->setFlash('success', 'GlobalAward has been saved!');
                   return $this->redirect($this->generateUrl('admin_global_award_index'));
                    
               }
           }

        $formAction = $id
            ? $this->generateUrl('admin_global_award_update', array('id' => $id))
            : $this->generateUrl('admin_global_award_create');

        return $this->render('AdminBundle:GlobalAward:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction,
        ));
    }

    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $global_award = $em->getRepository('HelperBundle:GlobalAward')->find($id);

        if ($global_award) {
            $global_award->setStatus($global_award->getStatus() ? 0 : 1);
            $em->persist($global_award);
            $em->flush($global_award);

            // dispatch event
            //$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_AFFILIATION, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_AFFILIATION, $global_award));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}