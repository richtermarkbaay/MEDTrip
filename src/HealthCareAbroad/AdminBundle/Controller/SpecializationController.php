<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;
use HealthCareAbroad\MedicalProcedureBundle\Form\MedicalCenterType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class SpecializationController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_CENTERS')")
     */
    public function indexAction()
    {
        return $this->render('AdminBundle:Specialization:index.html.twig', array('specializations' => $this->filteredResult, 'pager' => $this->pager));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function addAction()
    {
        $form = $this->createForm(new MedicalCenterType(), new MedicalCenter());

        return $this->render('AdminBundle:Specialization:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),  
            'formAction' => $this->generateUrl('admin_specialization_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function editAction($id)
    {
        $specialization = $this->getDoctrine()->getEntityManager()
                ->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id);

        $form = $this->createForm(new MedicalCenterType(), $specialization);

        return $this->render('AdminBundle:Specialization:form.html.twig', array(
            'id' => $id,
            'specialization' => $specialization,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_specialization_update', array('id' => $id))
        ));
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_CENTER')")
     */
    public function saveAction()
    {
        $request = $this->getRequest();    
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $specialization = $id
                ? $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id) 
                : new MedicalCenter();

        $form = $this->createForm(new MedicalCenterType(), $specialization);
           $form->bind($request);

           if ($form->isValid()) {
               $em->persist($specialization);
               $em->flush($specialization);

            // dispatch event               
               $eventName = $id ? AdminBundleEvents::ON_EDIT_MEDICAL_CENTER : AdminBundleEvents::ON_ADD_MEDICAL_CENTER;
               $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $specialization));
               
               $request->getSession()->setFlash('success', 'Medical center saved!');

               if($request->get('submit') == 'Save')
                   return $this->redirect($this->generateUrl('admin_specialization_edit', array('id' => $specialization->getId())));
               else 
                   return $this->redirect($this->generateUrl('admin_specialization_add'));
           }

        $formAction = $id 
            ? $this->generateUrl('admin_specialization_update', array('id' => $id))
            : $this->generateUrl('admin_specialization_create');
        
        return $this->render('AdminBundle:Specialization:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction 
        ));                
    }
    
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_DELETE_MANAGE_MEDICAL_CENTER')")
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $specialization = $em->getRepository('MedicalProcedureBundle:MedicalCenter')->find($id);

        if ($specialization) {
            $specialization->setStatus($specialization->getStatus() ? MedicalCenter::STATUS_INACTIVE : MedicalCenter::STATUS_ACTIVE);
            $em->persist($specialization);
            $em->flush($specialization);
            
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_MEDICAL_CENTER, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_MEDICAL_CENTER, $specialization));
            
            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
    
}