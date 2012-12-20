<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\MedicalProcedureBundle\Entity\MedicalCenter;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterGroupStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionStatus;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

use HealthCareAbroad\HelperBundle\Form\ErrorReportFormType;
use HealthCareAbroad\HelperBundle\Event\CreateErrorReportEvent;
use HealthCareAbroad\HelperBundle\Event\ErrorReportEvent;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class InstitutionController extends Controller
{
    protected $institution;
    
    public function preExecute()
    {
        $request = $this->getRequest();

        if($request->get('institutionSlug')) {
            $criteria = array('status' => InstitutionStatus::ACTIVE, 'slug' => $request->get('institutionSlug'));
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->findOneBy($criteria);

            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid institution');                
            }
        }
        
    }

    public function profileAction($institutionSlug)
    {
        //$gallery = $this->getDoctrine()->getRepository('MediaBundle:Medi')
        return $this->render('FrontendBundle:Institution:profile.html.twig', array('institution' => $this->institution));
    }

    public function listingAction()
    {
        $request = $this->getRequest();

        if($request->get('centerSlug')) {
            $criteria = array('status' => MedicalCenter::STATUS_ACTIVE, 'slug' => $request->get('centerSlug'));
            $medicalCenter = $this->getDoctrine()->getRepository('MedicalProcedureBundle:MedicalCenter')->findOneBy($criteria);

            if(!$medicalCenter) {
                throw $this->createNotFoundException('Invalid Medical Center');
            }

            $criteria = array('status' => InstitutionMedicalCenterGroupStatus::APPROVED, 'medicalCenter' => $medicalCenter);
            $institutionMedicalCenter = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionMedicalCenter')->findOneBy($criteria);


            if(!$institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid Institution Medical Center');
            }
            
        }

        return $this->render('FrontendBundle:Institution:fullListing.html.twig', array('center' => $institutionMedicalCenter));
    }

    public function errorReportAction()
    {
        
    }
}