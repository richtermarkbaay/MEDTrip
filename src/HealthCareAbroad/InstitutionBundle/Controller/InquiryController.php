<?php
/**
 * Controller that handles all Inquiries
 * @author Chaztine
 */
namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;
use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;
use HealthCareAbroad\PagerBundle\Pager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InquiryController extends InstitutionAwareController
{
    public function preExecute()
    {
        parent::preExecute();
    }
    
    public function viewAllInquiriesAction(Request $request)
    {
    
        $tab = $request->get('tabName','all');
        $template = "InstitutionBundle:Inquiry:inquiries.html.twig";
        $inquiryArr = $this->get('services.institution')->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        return $this->render($template, array(
                        'institution' => $this->institution,
                        'inquiries' => \json_encode($inquiryArr, JSON_HEX_APOS),
                        'isInquiry' => true,
                        'tabName' => $tab
        ));
    }
    
    public function viewInquiryAction(Request $request)
    {
        $inquiryId = $request->get('id');
        $inquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')->findOneById($inquiryId);
        $this->get('services.institution')->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_READ);
    
        return $this->render('InstitutionBundle:Inquiry:view_inquiry.html.twig', array(
                        'inquiry' => $inquiry,
                        'isInquiry' => true,
                        'prevPath' => $this->getRequest()->headers->get('referer')
        ));
    }
    
    public function removeInquiryAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        $inquiryId = $request->get('id');
        $tab = $request->get('tabName');
        $inquiry = $this->getDoctrine()->getRepository('InstitutionBundle:InstitutionInquiry')->findOneById($inquiryId);
        $institutionService->setInstitutionInquiryStatus($inquiry, InstitutionInquiry::STATUS_DELETED);
        $inquiryArr = $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        $output = array('inquiryList' => $inquiryArr,
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
    
        return $response;
    
    }
    
    public function ajaxSetInstitutionInquiryStatusAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        $inquiryList = $request->get('inquiryListArr');
        $inquiryStatus = InstitutionInquiry::STATUS_READ;
        $tab = $request->get('tabName');
        if($request->get('status') == '1') {
            $inquiryStatus = InstitutionInquiry::STATUS_UNREAD;
        }
        $inquiries = $institutionService->setInstitutionInquiryListStatus($inquiryList, $inquiryStatus);
        $inquiryArr = $institutionService->getInstitutionInquiriesBySelectedTab($this->institution, $tab);
        $output = array('inquiryList' => $inquiryArr,
                        'readCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_READ),
                        'unreadCntr' => $institutionService->getInstitutionInquiriesByStatus($this->institution, InstitutionInquiry::STATUS_UNREAD));
        $response = new Response(\json_encode($output, JSON_HEX_APOS),200, array('content-type' => 'application/json'));
    
        return $response;
    }
}