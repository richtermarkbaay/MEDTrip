<?php
namespace HealthCareAbroad\AdminBundle\Controller;

use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\InstitutionBundle\Entity\MedicalProviderGroup;
use HealthCareAbroad\HelperBundle\Form\MedicalProviderGroupFormType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\Request;
class MedicalProviderGroupController extends Controller
{
    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_MEDICAL_PROVIDER_GROUP')")
     */
    public function indexAction()
    {
     	$medicalProviderGroup = $this->getDoctrine()->getRepository('InstitutionBundle:MedicalProviderGroup')->findAll();
    	
    	return $this->render('AdminBundle:MedicalProviderGroup:index.html.twig', array(
                'medicalProviderGroups' => $medicalProviderGroup
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROVIDER_GROUP')")
     */
    public function addAction()
    {
        $form = $this->createForm(New MedicalProviderGroupFormType(), new MedicalProviderGroup());

        return $this->render('AdminBundle:MedicalProviderGroup:form.html.twig', array(
            'id' => null,
            'form' => $form->createView(),
            'formAction' => $this->generateUrl('admin_medical_provider_group_create')
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROVIDER_GROUP')")
     */
    public function editAction($id)
    {
        $medicalProviderGroup = $this->getDoctrine()->getEntityManager()
                ->getRepository('InstitutionBundle:MedicalProviderGroup')->find($id);

        $form = $this->createForm(New MedicalProviderGroupFormType(), $medicalProviderGroup);

        return $this->render('AdminBundle:MedicalProviderGroup:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('admin_medical_provider_group_update', array('id' => $id))
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROVIDER_GROUP')")
     */
    public function saveAction(Request $request)
    {
        if('POST' != $request->getMethod()) {
            return new Response("Save requires POST method!", 405);
        }

        $id = $request->get('id', null);
        $em = $this->getDoctrine()->getEntityManager();

        $medicalProviderGroup = $id ? $em->getRepository('InstitutionBundle:MedicalProviderGroup')->find($id) : new MedicalProviderGroup();

        $form = $this->createForm(New MedicalProviderGroupFormType(), $medicalProviderGroup);
           $form->bind($request);

           if ($form->isValid()) {
               $em->persist($medicalProviderGroup);
               $em->flush();

               // dispatch event
               $eventName = $id ? AdminBundleEvents::ON_EDIT_MEDICAL_PROVIDER_GROUP : AdminBundleEvents::ON_ADD_MEDICAL_PROVIDER_GROUP;
               $this->get('event_dispatcher')->dispatch($eventName, $this->get('events.factory')->create($eventName, $medicalProviderGroup));
               
               $request->getSession()->setFlash('success', 'Medical Provider Group has been saved!');

               return $this->redirect($this->generateUrl('admin_medical_provider_group_index'));
        }

        $formAction = $id ? $this->generateUrl('admin_medical_provider_group_update', array('id' => $id)) : $this->generateUrl('admin_medical_provider_group_create');

        return $this->render('AdminBundle:MedicalProviderGroup:form.html.twig', array(
                'id' => $id,
                'form' => $form->createView(),
                'formAction' => $formAction
        ));
    }

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_MANAGE_MEDICAL_PROVIDER_GROUP')")
     */
    public function updateStatusAction($id)
    {
        $result = false;
        $em = $this->getDoctrine()->getEntityManager();
        $medicalProviderGroup = $em->getRepository('InstitutionBundle:MedicalProviderGroup')->find($id);

        if ($medicalProviderGroup) {
            $medicalProviderGroup->setStatus($medicalProviderGroup->getStatus() ? $medicalProviderGroup::STATUS_INACTIVE : $medicalProviderGroup::STATUS_ACTIVE);
            $em->persist($medicalProviderGroup);
            $em->flush($medicalProviderGroup);

            // dispatch event
            $this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_MEDICAL_PROVIDER_GROUP, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_MEDICAL_PROVIDER_GROUP, $medicalProviderGroup));

            $result = true;
        }

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}