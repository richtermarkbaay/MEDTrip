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
         
        $this->request = $this->getRequest();
    }
    
    public function doctorProfileAction(Request $request)
    {
        $form = $this->createForm(new InstitutionDoctorFormType(), $this->institutionDoctor);

        return $this->render('InstitutionBundle:Doctor:index.html.twig', array(
                    'doctor' => $this->institutionDoctor,
                     'institutionMedicalCenter' => $this->institutionMedicalCenter,
                    'form' => $form->createView()
        ));
    }
    
    /**
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
     * Ajax handler for loading tabbed contents in institution profile page
     *
     * @param Request $request
     */
    public function loadTabbedContentsAction(Request $request)
    {
        $content = $request->get('content');
        $output = array();
        $parameters = array('institutionMedicalCenter' => $this->institutionMedicalCenter);
    
        switch ($content) {
            case 'specializations':
                $parameters['specializations'] = $this->institutionDoctor->getSpecializations();
                $output['specializations'] = array('html' => $this->renderView('InstitutionBundle:Doctor:tabbedContent.doctorSpecialization.html.twig', $parameters));
                break;
        }
    
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
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

    public function ajaxUpdateDoctorByFieldAction(Request $request)
    {
        if ($request->isMethod('POST')) {
    
            if($request->get('firstName')){
               $this->institutionDoctor->setFirstName($request->get('firstName'));
               $this->institutionDoctor->setLastName($request->get('lastName'));
               
               $output['info']['firstName'] = $this->institutionDoctor->getFirstName();
               $output['info']['lastName'] = $this->institutionDoctor->getLastName();
            }else{
                $this->institutionDoctor->setDetails($request->get('details'));
                $output['info']['details'] = $this->institutionDoctor->getDetails();
            }
            
            $em = $this->getDoctrine()->getEntityManager();
    
            try {
                $em->persist($this->institutionDoctor);
                $em->flush();
            }
            catch (\Exception $e) {
                 
                return new Response($e->getMessage(),500);
            }
        }
    
        return new Response(\json_encode($output),200, array('content-type' => 'application/json'));
    }
}