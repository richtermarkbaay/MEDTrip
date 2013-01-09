<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

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

class InstitutionMedicalCenterController extends Controller
{
    protected $institutionMedicalCenter;
    
    public function preExecute()
    {
        $request = $this->getRequest();

        if($request->get('imcSlug')) {
            $criteria = array('slug' => $request->get('imcSlug'));

            $qb = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
            $qb->select('a, b, c, d, e, f, g')->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
               ->leftJoin('a.institution', 'b')
               ->leftJoin('b.country', 'c')
               ->leftJoin('b.city', 'd')
               ->leftJoin('a.institutionSpecializations', 'e')
               ->leftJoin('e.specialization', 'f')
               ->leftJoin('e.treatments', 'g')
               ->where('a.slug = :centerSlug')
               ->setParameter('centerSlug', $criteria['slug']);
            $this->institutionMedicalCenter = $qb->getQuery()->getOneOrNullResult();
            
            if(!$this->institutionMedicalCenter) {
                throw $this->createNotFoundException('Invalid institutionMedicalCenter');                
            }
        }
    }

    public function profileAction($institutionSlug)
    {
//         $institutionService = $this->get('services.institution');
//         $gallery = $this->institution->getGallery();
        $params = array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
//            'institutionBranches' => $institutionService->getBranches($this->institution)
        );

//         if($gallery && $gallery->getMedia()->count()) {
//             $mediaGallery = $gallery->getMedia()->toArray();
//             $params['featuredImage'] = $mediaGallery[array_rand($mediaGallery)];
//         }

        return $this->render('FrontendBundle:InstitutionMedicalCenter:profile.html.twig', $params);
    }

}