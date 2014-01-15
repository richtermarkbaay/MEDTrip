<?php

/**
 * 
 * @author Chaztine Blance
 *
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Doctrine\ORM\Query;
use HealthCareAbroad\FrontendBundle\Form\InstitutionInquiryFormType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;


class InquiryController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INQUIRIES')")
     */
    public function viewInstitutionInquiriesAction()
    {
        return $this->render('AdminBundle:Inquiry:institution_inquiry.html.twig', array(
            'inquiries' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }
    
    public function viewUnapprovedInstitutionInquiriesAction()
    {
//         $inquiries = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')
//             ->getUnapprovedInstitutionInquiries(array(), Query::HYDRATE_ARRAY);
        
//         $json = \json_encode($inquiries, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
        
        // form will be used for the csrf token for api calls
        $institutionInquiryForm = $this->createForm(new InstitutionInquiryFormType(), new InstitutionInquiry());
        
        return $this->render('AdminBundle:Inquiry:unapprovedInquiries.html.twig', array(
            'institutionInquiryForm' => $institutionInquiryForm->createView()
        ));
    }
    
    
    public function viewInquiriesAction()
    {
    	return $this->render('AdminBundle:Inquiry:index.html.twig', array(
    			'inquiries' => $this->filteredResult,
    			'pager' => $this->pager
    	));
    }
}