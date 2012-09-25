<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\Doctor;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorType;
class DoctorController extends InstitutionAwareController
{

    public function indexAction()
    {
        $institutionId = $this->institution->getId();        
        $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionDoctor')->getDoctorsList($institutionId);
        
        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                        'doctors' => $doctors
        ));
        
    }
    
    public function addAction(Request $request)
    {      
        $form = $this->createForm(new InstitutionDoctorSearchFormType());
        
        $institution = $this->institution->getName();
        $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->getActiveDoctors();
        return $this->render('InstitutionBundle:Doctor:add.html.twig', array(
                'form' => $form->createView(),
                'institution' => $institution,
                'doctorsJSON' => $doctors
    	));
    }
    
    public function editAction(Request $request)
    {
        $doctorId = $request->get('idId', 0);
        $institutionDoctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
        
        if (!$institutionDoctor) {
            throw $this->createNotFoundException("Invalid institution doctor.");
        }
        $form = $this->createForm(new InstitutionDoctorFormType(), $institutionDoctor);
        
        return $this->render('InstitutionBundle:Doctor:edit.html.twig', array(
                        'institutionDoctor' => $institutionDoctor,
                        'form' => $form->createView(),
        ));
    }
    
    /**
     * Saves an institution doctor
     *
     */
    public function saveAction(Request $request)
    {
        if ($doctorId = $request->get('idId', 0)) {
            $institutionDoctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
            echo get_class($institutionDoctor); 
            if (!$institutionDoctor) {
                throw $this->createNotFoundException("Invalid institution doctor.");
            }
        }
        else {
            $institutionDoctor = new InstitutionDoctor();
            $institutionDoctor->setInstitution($this->institution);
        }
        $isNew = $institutionDoctor->getId() == 0;
        
        $form = $this->createForm(new InstitutionDoctorFormType(), $institutionDoctor);
        $form->bind($request);
    
        if ($form->isValid()) {
    
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionDoctor);
            $em->flush();
    
            $request->getSession()->setFlash('success', "Successfully ".($isNew?'added':'updated')." account");
    
            return $this->redirect($this->generateUrl('institution_doctor_edit', array('idId' => $institutionDoctor->getId())));
        }
        else {
    
            return $this->render($isNew ? 'InstitutionBundle:Doctor:add.html.twig': 'InstitutionBundle:Doctor:edit.html.twig', array(
                            'form' => $form->createView(),
                            'isNew' => $isNew,
                            'institutionDoctor' => $institutionDoctor
            ));
        }
    }
}