<?php
/*
 * author: Alnie Jacobe
 */
namespace HealthCareAbroad\AdminBundle\Controller;
use HealthCareAbroad\HelperBundle\Entity\ContactDetailTypes;

use HealthCareAbroad\HelperBundle\Entity\ContactDetail;

use HealthCareAbroad\DoctorBundle\Form\DoctorFormType;
use HealthCareAbroad\AdminBundle\Event\AdminBundleEvents;
use HealthCareAbroad\DoctorBundle\Entity\Doctor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\AdminBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class DoctorController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Doctor:index.html.twig', array(
            'doctors' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }
    
    public function searchMedicalSpecialistSpecializationAction(Request $request)
    {
        $doctorId = $request->get('doctorId');
        $doctor = $this->getDoctrine()->getRepository("DoctorBundle:Doctor")->find($doctorId);
        $specializations = $this->getDoctrine()->getRepository("DoctorBundle:Doctor")->getSpecializationByMedicalSpecialist($doctorId);
        
        $specializationsData = '';
        //construct specialization data
        foreach($specializations as $each) {
            $specializationsData .= $each['name']."<br>";
        }
        
        // construct the row for a medical specialist
        $html = '<tr id="doctor"'.$doctorId.'"><td><h5>'.$doctor->getFirstName() ." ". $doctor->getLastName().'</h5>'.$specializationsData.'</td><td><input class="btn btn-danger award_deleteBtn" type="button" onclick="DoctorAuto.deleteRow($(this),'.$doctorId.')" value="Remove"></td></tr>';
        return new Response(\json_encode($html),200, array('content-type' => 'application/json'));
    }
    
    public function editAction(Request $request)
    {
        $specializations = $this->getDoctrine()->getRepository('TreatmentBundle:Specialization')->findAll();
        
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
        
        $this->get('services.contact_detail')->initializeContactDetails($doctor, array(ContactDetailTypes::PHONE));
        
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
            
            $media = $doctor->getMedia();
            $msg = "Successfully updated doctors profile";
            $title = 'Edit Doctor Details';
        }
        else {
            $doctor = new Doctor();
            $doctor->setStatus(Doctor::STATUS_ACTIVE);
            $msg = "Successfully added doctor";
            $title = 'Add Doctor Details';
        }

        $this->get('services.contact_detail')->initializeContactDetails($doctor, array(ContactDetailTypes::PHONE));

        $form = $this->createForm(new DoctorFormType(), $doctor);
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($request);
            if($form->isValid()) {
                $fileBag = $request->files->get('doctor');
                if(isset($fileBag['media']) && $fileBag['media']) {
                    $this->get('services.doctor.media')->uploadLogo($fileBag['media'], $doctor, false);
                }

                if($medicalSpecialitiesIds = $request->get('doctor_medical_specialities', array())) {
                    $qb = $this->getDoctrine()->getEntityManagerForClass('DoctorBundle:MedicalSpeciality')->createQueryBuilder();
                    $qb->select('a')->from('DoctorBundle:MedicalSpeciality', 'a')
                       ->where($qb->expr()->in('a.id', ':medicalSpecialitiesIds'))
                       ->setParameter(':medicalSpecialitiesIds', $medicalSpecialitiesIds);

                    $medicalSpecialities = $qb->getQuery()->getResult();

                    // Add selected medicalSpecialities
                    foreach ($medicalSpecialities as $each) {
                        if(!$doctor->getMedicalSpecialities()->contains($each)) {
                            $doctor->addMedicalSpeciality($each);                        
                        }
                    }
                }

                // Remove non-selected medicalSpecialities
                foreach($doctor->getMedicalSpecialities() as $each) {
                    if(!in_array($each->getId(), $medicalSpecialitiesIds)) {
                        $doctor->removeMedicalSpeciality($each);
                    }
                }
                
                $this->get('services.contact_detail')->removeInvalidContactDetails($doctor);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($doctor);
                $em->flush();

                if ($doctor) {
                    $data = $request->get('doctor');
                    if(count($data['specializations']) > 1)
                    {
                        $this->get('session')->setFlash('info', 'Successfully added doctor! Note: you have added multiple specializations to this doctor.');
                    }else{
                        $this->get('session')->setFlash('success', $msg);
                    }
                    
                    return $this->redirect($this->generateUrl('admin_doctor_index'));
                } else {
                    $this->get('session')->setFlash('error', "Unable to update account");
                }
            }
        }
        return $this->render('AdminBundle:Doctor:edit.html.twig', array(
                        'doctor' => $doctor,
                        'form' => $form->createView(),
                        'title' => $title
        ));
    }
    
    /**
     * Update status
     */
    public function updateStatusAction(Request $request)
    {
    	$result = false;
    	$em = $this->getDoctrine()->getEntityManager();
    	$doctor = $em->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId', 0));
  
    	if ($doctor) {
    		$doctor->setStatus($doctor->getStatus() ? $doctor::STATUS_INACTIVE : $doctor::STATUS_ACTIVE);
    		$em->persist($doctor);
    		$em->flush($doctor);
    
    		// dispatch event
    		$this->get('event_dispatcher')->dispatch(AdminBundleEvents::ON_EDIT_DOCTOR, $this->get('events.factory')->create(AdminBundleEvents::ON_EDIT_DOCTOR, $doctor));
    		$result = true;
    	}
  
    	$response = new Response(json_encode($result));
    	$response->headers->set('Content-Type', 'application/json');
    
    	return $response;
    }
    
}