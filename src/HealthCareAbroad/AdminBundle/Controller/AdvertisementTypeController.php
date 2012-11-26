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
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEWADVERTISEMENT_TYPES')")
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

        $properties = $this->getDoctrine()->getRepository('AdvertisementBundle:AdvertisementPropertyName')->findByStatus(AdvertisementPropertyName::STATUS_ACTIVE);
 
        $form = $this->createForm(New AdvertisementTypeFormType($properties), new AdvertisementType());

        if($request->isMethod(Request::POST)) {
            $advertisementType = $request->get('advertisementType');
            $propertyNames = $advertisementType['advertisementTypeConfigurations'];
            
            $form->bind($advertisementType);
            if($form->isValid()) {
                $advertisementType = $form->getData();
                
                //var_dump($advertisementType);
                
                $advertisementType->setStatus(AdvertisementType::STATUS_ACTIVE);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($advertisementType);
                $em->flush();
            
                
            }            
        }
        

        
        return $this->render('AdminBundle:AdvertisementType:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_advertisement_type_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT_TYPE')")
     */
    public function editAction($id)
    {
        $advertisementType = $this->getDoctrine()->getEntityManager()
                ->getRepository('Advertisement:AdvertisementType')->find($id);

        $form = $this->createForm(New CountryFormType(), $advertisementType);

        return $this->render('AdminBundle:AdvertisementType:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_advertisement_type_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_ADVERTISEMENT_TYPE')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $advertisementType = $id ? $em->getRepository('Advertisement:AdvertisementType')->find($id) : new Country();

        $form = $this->createForm(New CountryFormType(), $advertisementType);
           $form->bind($request);

           if ($form->isValid()) {
               $em->persist($advertisementType);
               $em->flush($advertisementType);

               // dispatch event
               $eventName = $id ? AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE : AdminBundleEvents::ON_ADD_ADVERTISEMENT_TYPE;
               $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $advertisementType));
               
               $request->getSession()->setFlash('success', 'Country has been saved!');

               return $this->redirect($this->generateUrl('admin_advertisement_type_index'));
        }

        $formAction = $id ? $this->generateUrl('admin_advertisement_type_update', array('id' => $id)) : $this->generateUrl('admin_advertisement_type_create');

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
        $advertisementType = $em->getRepository('Advertisement:AdvertisementType')->find($id);

        if ($advertisementType) {
            $advertisementType->setStatus($advertisementType->getStatus() ? $advertisementType::STATUS_INACTIVE : $advertisementType::STATUS_ACTIVE);
            $em->persist($advertisementType);
            $em->flush($advertisementType);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_ADVERTISEMENT_TYPE, $advertisementType));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}