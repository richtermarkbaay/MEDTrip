<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

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
            $qb->select('a, b, c, d, e, f, g, h')->from('InstitutionBundle:Institution', 'a')
               ->leftJoin('a.institutionMedicalCenters', 'b')
               ->leftJoin('b.institutionSpecializations', 'c')
               ->leftJoin('c.specialization', 'd')
               ->leftJoin('c.treatments', 'e')
               ->leftJoin('a.country', 'f')
               ->leftJoin('a.city', 'g')
               ->leftJoin('a.logo', 'h')
               ->where('a.slug = :institutionSlug')
               ->setParameter('institutionSlug', $criteria['slug']);

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
            'form' => $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry())->createView()		
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
        

        if($gallery && $gallery->getMedia()->count()) {
            $mediaGallery = $gallery->getMedia()->toArray();
            $params['featuredImage'] = $mediaGallery[array_rand($mediaGallery)];
        }
        

        return $this->render('FrontendBundle:Institution:profile.html.twig', $params);
    }
    
    public function ajaxSaveInstitutionInquiryAction(Request $request)
    {
        $institutionInquiry = new InstitutionInquiry();
        $form = $this->createForm(new InstitutionInquiryFormType(), $institutionInquiry);
        
        if ($request->isMethod('POST')) {
             
            $form->bindRequest($request);
             
            if ($form->isValid()) {
                $institutionInquiry->setStatus(InstitutionInquiry::STATUS_SAVE);
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($institutionInquiry);
                $em->flush();                
                
                $this->get('session')->setFlash('notice', "Successfully saved!");
                 
            }
        }
        $response = new Response(\json_encode(array('id' => $institutionInquiry->getId())), 200, array('content-type' => 'application/json'));
        return $response;
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