<?php
/*
 * author: Alnie L. Jacobe
 * Adds doctor to institution, 
 * Update doctor details and 
 * Create new doctor
 * Delete doctor to an institution
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;
use HealthCareAbroad\InstitutionBundle\Repository\DoctorRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\Doctor;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionDoctor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class DoctorController extends InstitutionAwareController
{

    /*
     * displays all doctors to the institution
     */
    public function indexAction()
    {
        $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionDoctor')->getDoctorsList($this->institution->getId());
        
        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                        'doctors' => $doctors
        ));
    }
    
    /*
     * first step on adding doctor
     */
    public function addAction(Request $request)
    {      
        $form = $this->createForm(new InstitutionDoctorSearchFormType());
        
        return $this->render('InstitutionBundle:Doctor:add.html.twig', array(
                'form' => $form->createView(),
                'institution' => $this->institution
    	));
    }
    
    /*
     * edit doctor details
     */
    public function editAction(Request $request)
    {
        echo  $doctorId = $request->get('idId', 0);exit;
        $doctorId = $request->get('idId', 0);
        $institutionDoctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
        
        if (!$institutionDoctor) {
            throw $this->createNotFoundException("Invalid institution doctor.");
        }
        $form = $this->createForm(new InstitutionDoctorFormType(), $institutionDoctor);
        
        return $this->render('InstitutionBundle:Doctor:edit.html.twig', array(
                        'institutionDoctor' => $institutionDoctor,
                        'form' => $form->createView()
        ));
    }
    
    /*
     * create new doctor | add doctor to an institution
     */
    public function saveAction(Request $request)
    {
        $institutionDoctor = new InstitutionDoctor();
        $institutionDoctor->setInstitution($this->institution);
        
        $doctor = new Doctor();
        $form = $this->createForm(new InstitutionDoctorFormType(), $doctor);
        $form->bind($request);
        
        if ($doctorId = $request->get('idId', 0)) {
            $doctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
            if (!$doctor) {
                throw $this->createNotFoundException("Invalid doctor.");
            }
        }
        else {
            
            if ($form->isValid()) {                
                $doctor->setStatus(Doctor::STATUS_ACTIVE);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush();
            }
        }
        
        $institutionDoctor->setDoctor($doctor);
        $institutionDoctor->setStatus(InstitutionDoctor::STATUS_ACTIVE);
    
        if ($form->isValid()) {
    
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($institutionDoctor);
            $em->flush();
    
            $request->getSession()->setFlash('success', "Successfully added account");
            $doctors = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionDoctor')->getDoctorsList($this->institution->getId());
            
            return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                            'doctors' => $doctors
            ));
        }
    }
    
    /**
     * Update details of an institution doctor
     *
     */
    public function updateAction(Request $request)
    {
        if ($doctorId = $request->get('idId', 0)) {
            $institutionDoctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
            if (!$institutionDoctor) {
                throw $this->createNotFoundException("Invalid institution doctor.");
            }
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
        
    }
    
    /*
     * Get doctors list that is not assigned to Institution
     */
    public function searchAction(Request $request)
    {
        $searchTerm = $request->get('name_startsWith');
        $data = array();
        $doctors = $this->getDoctrine()->getRepository("InstitutionBundle:Doctor")->getDoctorsBySearchTerm($searchTerm, $this->institution->getId());
        
        foreach($doctors as $each) {
            $data[] = array('id' => $each->getId(), 
                            'firstName' => $each->getFirstName(),
                            'middleName' => $each->getMiddleName(),
                            'lastName' => $each->getLastName());
        }
        
        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }
    
    /*
     * display doctor details form
     */
    public function addDetailAction(Request $request)
    {
        $form = $this->createForm(new InstitutionDoctorSearchFormType());
        $form->bind($request);
        
        if($request->isMethod('POST')) {
            if($form->isValid()){
                $doctorId = $form->get('id')->getData();
                $doctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
                $form = $this->createForm(new InstitutionDoctorFormType(),$doctor);
            }
        }
        
        return $this->render('InstitutionBundle:Doctor:addDetail.html.twig', array(
                        'form' => $form->createView(),
                        'institution' => $this->institution,
                        'idId' => $doctorId
        ));
    }
    
    /*
     * delete InstitutionDoctor
     */
    public function deleteAction(Request $request) 
    {
        if( $doctorId = $request->get('idId', 0)) {
            $doctor = $this->getDoctrine()->getRepository('InstitutionBundle:Doctor')->find($doctorId);
            $institutionDoctor = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionDoctor')->findOneBy(array('institution' => $this->institution, 'doctor' => $doctor));
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($institutionDoctor);
            $em->flush();
            
            $request->getSession()->setFlash('success', "Successfully removed account");
            
        }
        return $this->redirect($this->generateUrl('institution_view_all_doctors'));
        
    }
}