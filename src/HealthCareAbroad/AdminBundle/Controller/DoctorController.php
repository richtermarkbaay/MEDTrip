<?php
/*
 * author: Alnie Jacobe
 */
namespace HealthCareAbroad\AdminBundle\Controller;
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
        $doctors = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->findAll();
        return $this->render('AdminBundle:Doctor:index.html.twig', array(
                        'doctors' => $doctors
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
            $specializationsData .= $each['name'] ."<br>";
        }
    
        // construct the row for a medical specialist
        $html = '<tr id="doctor"'.$doctorId.'"><td><h5>'.$doctor->getFirstName() ." ". $doctor->getLastName().'</h5><br>'.$specializationsData.'</td><td><input class="btn btn-danger award_deleteBtn" type="button" onclick="DoctorAuto.deleteRow($(this),'.$doctorId.')" value="Remove"></td></tr>';
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
        
        $doctor->setMedia(null);
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
            $media = $doctor->getMedia();
            $doctor->setMedia(null);
            
            if (!$doctor) {
                throw $this->createNotFoundException("Invalid doctor.");
            }
            $msg = "Successfully updated account";
            $title = 'Edit Doctor Details';
        }
        else {
            $doctor = new Doctor();
            $doctor->setStatus(Doctor::STATUS_ACTIVE);
            $msg = "Successfully added doctor";
            $title = 'Add Doctor Details';
        }
        
        $form = $this->createForm(new DoctorFormType(), $doctor);
        
        if ($this->getRequest()->isMethod('POST')) {
            $doctorData = $request->get('doctor');
            
            if($newMedia = $this->saveMedia($request->files->get('doctor'))) {
                $doctorData['media'] = $newMedia;                
            } else {
                if($doctor->getId()) {
                    $doctorData['media'] = $media;
                }
            }

            $form->bind($doctorData);

            if($form->isValid()) {
                // Get contactNumbers and convert to json format
                $contactNumber = json_encode($request->get('contactNumber'));

                $doctor->setContactNumber($contactNumber);
                $em = $this->getDoctrine()->getEntityManager();

                $em->persist($doctor);
                $em->flush();

                if ($doctor) {
                    $this->get('session')->setFlash('success', $msg);
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
    	$doctor = $em->getRepository('DoctorBundle:Doctor')->find($request->get('idId', 0));
  
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
    
    private function saveMedia($fileBag)
    {
        if($fileBag['media']) {
            $media = $this->get('services.media')->uploadDoctorImage($fileBag['media']);
            return $media;
        }

        return null;
    }
}