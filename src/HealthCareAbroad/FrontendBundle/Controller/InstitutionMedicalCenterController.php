<?php
/**
 *
 * @author Adelbert Silla
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\AdminBundle\Entity\ErrorReport;

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
            $qb->select('a, b, c, d, e, f, g, h')->from('InstitutionBundle:InstitutionMedicalCenter', 'a')
               ->leftJoin('a.institution', 'b')
               ->leftJoin('b.country', 'c')
               ->leftJoin('b.city', 'd')
               ->leftJoin('a.institutionSpecializations', 'e')
               ->leftJoin('e.specialization', 'f')
               ->leftJoin('e.treatments', 'g')
               ->leftJoin('g.subSpecializations', 'h')
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
         $centerService = $this->get('services.institution_medical_center');
//         $gallery = $this->institution->getGallery();
        $params = array(
            'institutionMedicalCenter' => $this->institutionMedicalCenter,
            'awards' => $centerService->getMedicalCenterGlobalAwards($this->institutionMedicalCenter),
            'services' => $centerService->getMedicalCenterServices($this->institutionMedicalCenter),
        );

//         if($gallery && $gallery->getMedia()->count()) {
//             $mediaGallery = $gallery->getMedia()->toArray();
//             $params['featuredImage'] = $mediaGallery[array_rand($mediaGallery)];
//         }

        return $this->render('FrontendBundle:InstitutionMedicalCenter:profile.html.twig', $params);
    }

}