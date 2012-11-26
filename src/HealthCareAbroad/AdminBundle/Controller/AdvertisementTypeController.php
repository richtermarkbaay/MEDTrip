<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use Guzzle\Http\Message\Request;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementPropertyName;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementType;

use HealthCareAbroad\AdvertisementBundle\Form\AdvertisementTypeFormType;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class AdvertisementTypeController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_ADVERTISEMENT_TYPES')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:AdvertisementType:index.html.twig', array(
            'advertisementTypes' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT_TYPE')")
     */
    public function addAction()
    {
        $request = $this->getRequest();
 
        $form = $this->createForm(New AdvertisementTypeFormType(), new AdvertisementType());

        return $this->render('AdminBundle:AdvertisementType:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_advertisementType_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT_TYPE')")
     */
    public function editAction($id)
    {
        $advertisementType = $this->getDoctrine()->getEntityManager()->getRepository('AdvertisementBundle:AdvertisementType')->find($id);

        $form = $this->createForm(New AdvertisementTypeFormType(), $advertisementType);

        return $this->render('AdminBundle:AdvertisementType:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_advertisementType_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT_TYPE')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        
        if(!$request->isMethod(Request::POST)) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $advertisementType = $id ? $em->getRepository('AdvertisementBundle:AdvertisementType')->find($id) : new AdvertisementType();

        $form = $this->createForm(New AdvertisementTypeFormType(), $advertisementType);
        
        $form->bind($request);

        if ($form->isValid()) {
            $advertisementType = $form->getData();
            $advertisementType->setStatus(AdvertisementType::STATUS_ACTIVE);
            $em->persist($advertisementType);
            $em->flush();

            // dispatch event
            //$eventName = $id ? AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE : AdminBundleEvents::ON_ADD_ADVERTISEMENT_TYPE;
            //$this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $advertisementType));
           
            $request->getSession()->setFlash('success', 'Advertisement type has been saved!');
        
            return $this->redirect($this->generateUrl('admin_advertisementType_index'));
        }

        $formAction = $id ? $this->generateUrl('admin_advertisementType_update', array('id' => $id)) : $this->generateUrl('admin_advertisementType_create');

        return $this->render('AdminBundle:AdvertisementType:form.html.twig', array(
            'id' => $id,
            'form' => $form->createView(),
            'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_ADVERTISEMENT_TYPE')")
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $advertisementType = $em->getRepository('AdvertisementBundle:AdvertisementType')->find($id);

        if ($advertisementType) {
            $advertisementType->setStatus($advertisementType->getStatus() ? $advertisementType::STATUS_INACTIVE : $advertisementType::STATUS_ACTIVE);
            $em->persist($advertisementType);
            $em->flush();

            // dispatch event
            //$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE, $advertisementType));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}