<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use Doctrine\ORM\Query\Expr\Join;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionPropertyType;

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
            $criteria = array('slug' => $request->get('institutionSlug'));
            $this->institution = $this->getDoctrine()->getRepository('InstitutionBundle:Institution')->findOneBy($criteria);

            $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f, g, h, i, j')->from('InstitutionBundle:Institution', 'a')
               ->leftJoin('a.institutionMedicalCenters ', 'b', Join::WITH, 'b.status = :medicalCenterStatus')
               ->leftJoin('b.institutionSpecializations', 'c')
               ->leftJoin('c.specialization', 'd')
               ->leftJoin('c.treatments', 'e')
               ->leftJoin('a.country', 'f')
               ->leftJoin('a.city', 'g')
               ->leftJoin('a.logo', 'h')
               ->leftJoin('b.doctors', 'i')
               ->leftJoin('i.specializations', 'j')
               ->where('a.slug = :institutionSlug')
               ->andWhere('a.status = :status')
               ->setParameter('institutionSlug', $criteria['slug'])
               ->setParameter('status', InstitutionStatus::getBitValueForApprovedStatus())
               ->setParameter('medicalCenterStatus', InstitutionMedicalCenterStatus::APPROVED);

            $this->institution = $qb->getQuery()->getOneOrNullResult();

            
            if(!$this->institution) {
                throw $this->createNotFoundException('Invalid institution');                
            }
        }
        
    }

    public function profileAction($institutionSlug)
    {
        $institutionService = $this->get('services.institution');
        $gallery = $this->institution->getGallery();

        $params = array(
            'institution' => $this->institution,
            'isSingleCenterInstitution' => $institutionService->isSingleCenter($this->institution),
            'institutionDoctors' => $institutionService->getAllDoctors($this->institution),
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView(),
            'formId' => 'institution_inquiry_form'	
                        	
//            'institutionBranches' => $institutionService->getBranches($this->institution)
        );

        if($params['isSingleCenterInstitution']) {
            $centerService = $this->get('services.institution_medical_center');
            $this->institutionMedicalCenter = $institutionService->getFirstMedicalCenter($this->institution);
            $params['institutionAwards'] = $centerService->getMedicalCenterGlobalAwards($this->institutionMedicalCenter);
            $params['institutionServices'] = $centerService->getMedicalCenterServices($this->institutionMedicalCenter);
        } else {
            $params['institutionAwards'] = $institutionService->getAllGlobalAwards($this->institution);
            $params['institutionServices'] = $institutionService->getInstitutionServices($this->institution);
        }

        return $this->render('FrontendBundle:Institution:profile.html.twig', $params);
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