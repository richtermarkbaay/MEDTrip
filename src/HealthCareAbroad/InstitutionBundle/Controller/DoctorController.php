<?php
/**
 * @author Chaztine Blance
 * Manage Doctors Profile
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
    public function doctorProfileAction(Request $request)
    {
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findAll();
        
        if($doctorId = $request->get('idId', 0)) {
            $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($doctorId);
            if (!$doctor) {
                throw $this->createNotFoundException("Invalid doctor.");
            }
            $title = 'Edit Doctor Details';
        }
        
        $form = $this->createForm(new InstitutionDoctorFormType(), $doctor);

        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                    'doctor' => $doctor,
                    'form' => $form->createView()
        ));
    }
    
    /**
     * Update doctor details
    */
    public function saveAction(Request $request)
    {
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('idId', 0));

        if (!$doctor) {
            throw $this->createNotFoundException("Invalid doctor.");
        }
        $form = $this->createForm(new InstitutionDoctorFormType(), $doctor);
    
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($request);
            
            // Get contactNumbers and convert to json format
            $contactNumber = json_encode($_POST['institutionDoctor']['contactNumber']);
            
            if($form->isValid()) {
             
                $form->getData()->setContactNumber($contactNumber);
             
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($form->getData());
                $em->flush();
                
                $this->get('session')->setFlash('notice', "Successfully updated profile");
            }
        }
        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                        'doctor' => $doctor,
                        'form' => $form->createView()
        ));
    }
}