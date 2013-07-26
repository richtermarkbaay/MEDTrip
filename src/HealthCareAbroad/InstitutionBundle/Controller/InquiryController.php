<?php
/**
 * Controller that handles all Inquiries
 * @author Chaztine
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InquiryController extends InstitutionAwareController
{
    protected $inquiryRepo;
    
    public function preExecute()
    {
        parent::preExecute();
        
        $this->inquiryRepo = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry');
    }
    
    public function viewAllInquiriesAction(Request $request)
    {
        $tab = $request->get('tabName','all');
        $template = "InstitutionBundle:Inquiry:inquiries.html.twig";
        $inquiries = $this->get('services.institution')->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        
        return $this->render($template, array(
                        'institution' => $this->institution,
                        'inquiries' => \json_encode($inquiries, JSON_HEX_APOS),
                        'isInquiry' => true,
                        'tabName' => $tab
        ));
    }
    
    public function viewInquiryAction(Request $request)
    {
        if(!$inquiry = $this->inquiryRepo->findOneById($request->get('id'))) {
            throw $this->createNotFoundException('Invalid Inquiry Id');
        }
        $this->get('services.institution')->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_READ);
    
        return $this->render('InstitutionBundle:Inquiry:view_inquiry.html.twig', array(
                        'inquiry' => $inquiry,
                        'isInquiry' => true,
                        'prevPath' => $this->getRequest()->headers->get('referer')
        ));
    }
    
    public function ajaxRemoveInquiryAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        if(!$inquiry = $this->inquiryRepo->findOneById($request->get('id'))) {
            throw $this->createNotFoundException('Invalid Inquiry Id');
        }
        
        $institutionService->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_DELETED);
        $output = array('inquiryList' => $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $request->get('tabName')),
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
    
        return $response;
    
    }
    
    public function ajaxSetInstitutionInquiryStatusAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        
        $inquiryStatus = InstitutionInquiry::STATUS_READ;
        if($request->get('status') == '1') {
            $inquiryStatus = InstitutionInquiry::STATUS_UNREAD;
        }
        
        if($inquiryList = $request->get('inquiryListArr')) {
            $inquiries = $institutionService->setInstitutionInquiryListStatus($inquiryList, $inquiryStatus);
        }
       
        $output = array('inquiryList' => $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $request->get('tabName')),
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
    
        return $response;
    }
}