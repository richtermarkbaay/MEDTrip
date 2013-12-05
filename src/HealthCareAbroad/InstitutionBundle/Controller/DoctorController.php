<?php
/**
 * Manage Doctors Profile
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;
use HealthCareAbroad\InstitutionBundle\Repository\DoctorRepository;

use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorSearchFormType;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionDoctorFormType;

use HealthCareAbroad\InstitutionBundle\Entity\Doctor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
class DoctorController extends InstitutionAwareController
{
    /**
     * 
     * @var InstitutionDoctor
     */
    protected $institutionDoctor; 

    /**
     * @var InstitutionMedicalCenter
     */
    private $institutionMedicalCenter = null;
    
    public function preExecute()
    {
        parent::preExecute();
        $this->repository = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter');
        
        if ($idId=$this->getRequest()->get('idId',0)) {
            $this->institutionDoctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($idId);
        }
       
        if ($imcId=$this->getRequest()->get('imcId',0)) {
            $this->institutionMedicalCenter = $this->repository->find($imcId);
        }
    }
    
    
    /**
     * @deprecated ?? - currently not being used!
     * Update doctor details
    */
    public function saveAction(Request $request)
    {
        $form = $this->createForm(new InstitutionDoctorFormType(), $this->institutionDoctor);
    
        if ($this->getRequest()->isMethod('POST')) {
            $form->bind($request);
            
            // Get contactNumbers and convert to json format
            $contactNumber = json_encode($_POST['institutionDoctor']['contactNumber']);
            
            if($form->isValid()) {
             
                $this->institutionDoctor = $form->getData();
                $this->institutionDoctor->setContactNumber($contactNumber);
             
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($this->institutionDoctor);
                $em->flush();
                
                $this->get('session')->setFlash('notice', "Successfully updated profile");
            }
        }
        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                        'doctor' => $this->institutionDoctor,
                         'institutionMedicalCenter' => $this->institutionMedicalCenter,
                        'form' => $form->createView()
        ));
    }

    /**
     * Upload logo for Doctor - UPDATED FUNCTION, DO NOT REMOVE
     * @param Request $request
     */
    public function uploadAction(Request $request)
    {
        $data = array('status' => false);
        $doctor = $this->getDoctrine()->getRepository('DoctorBundle:Doctor')->find($request->get('doctorId'));


        if (($file = $request->files->get('file')) && $doctor) {
            $media = $this->get('services.doctor.media')->uploadLogo($file, $doctor, true);
            $data['status'] = true;
            $data['doctor'] = $this->get('services.doctor')->toArrayDoctor($doctor);
        }

        return new Response(\json_encode($data), 200, array('content-type' => 'application/json'));
    }
}