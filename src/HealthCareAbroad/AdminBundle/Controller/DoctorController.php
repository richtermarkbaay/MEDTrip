<?php
/*
 * author: Alnie Jacobe
 */
namespace HealthCareAbroad\AdminBundle\Controller;
use HealthCareAbroad\DoctorBundle\Form\DoctorFormType;

use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\AdminBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class DoctorController extends Controller
{
    public function indexAction()
    {
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->findAll();
        return $this->render('AdminBundle:Doctor:index.html.twig', array(
                        'doctors' => $doctors
        ));
    }
    
    public function editAction(Request $request)
    {
        
        if($doctorId = $request->get('idId', 0)) {
            $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($doctorId);
            if (!$doctor) {
                throw $this->createNotFoundException("Invalid doctor.");
            }
            $title = 'Edit Doctor Details';
        }
        else {
            $doctor = new Doctor();
            $title = 'Add Doctor Details';
        }
        
        $form = $this->createForm(new DoctorFormType(), $doctor);
    
        return $this->render('AdminBundle:Doctor:edit.html.twig', array(
                        'doctor' => $doctor,
                        'form' => $form->createView(),
                        'title' => $title
        ));
    } 
    
    /*
     * create new doctor | update doctor details
    */
    public function saveAction(Request $request)
    {
        
        if ($doctorId = $request->get('idId', 0)) {
            $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($doctorId);
            if (!$doctor) {
                throw $this->createNotFoundException("Invalid doctor.");
            }
            $msg = "Successfully updated account";
        }
        else {
            $doctor = new Doctor();
            $doctor->setStatus(Doctor::STATUS_ACTIVE);
            $msg = "Successfully added doctor";
        }
        
        $form = $this->createForm(new DoctorFormType(), $doctor);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bindRequest($this->getRequest());

            if($form->isValid()) {

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush();

                if ($doctor) {
                    $this->get('session')->setFlash('success', $msg);
                    
                } else {
                    $this->get('session')->setFlash('error', "Unable to update account");
                }
            }
        }
        return $this->redirect($this->generateUrl('admin_doctor_index'));

    }
}