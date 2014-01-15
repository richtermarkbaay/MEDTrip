<?php
/**
 * Controller that handles all Inquiries
 * 
 * @author Adelbert Silla - updated on 01-15-2014
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
        $inquiries = $this->get('services.institution.inquiry')->getInquiriesByInstitution($this->institution);

        return $this->render('InstitutionBundle:Inquiry:inquiries.html.twig', array(
            'inquiries' => $inquiries
        ));
    }
    
    public function viewInquiryAction(Request $request)
    {
        if(!$inquiry = $this->inquiryRepo->findOneById($request->get('id'))) {
            throw $this->createNotFoundException('Invalid Inquiry');
        }

        if($inquiry->getStatus() == InstitutionInquiry::STATUS_UNREAD) {
            $this->get('services.institution.inquiry')->updateInquiryStatus($inquiry, InstitutionInquiry::STATUS_READ);
            $this->clearUnreadInquiriesSession();
            $currentCount = $this->getRequest()->getSession()->get('unreadInquiriesCount');
            $this->getRequest()->getSession()->set('unreadInquiriesCount', ($currentCount-1));
        }

        return $this->render('InstitutionBundle:Inquiry:view_inquiry.html.twig', array(
            'inquiry' => $inquiry,
            'prevPath' => $this->getRequest()->headers->get('referer')
        ));
    }
    
    public function ajaxRemoveInquiryAction(Request $request)
    {
        $institutionService = $this->get('services.institution');
        if(!$inquiry = $this->inquiryRepo->findOneById($request->get('id'))) {
            throw $this->createNotFoundException('Invalid Inquiry');
        }

        if($inquiry->getStatus() == InstitutionInquiry::STATUS_UNREAD) {
            $this->clearUnreadInquiriesSession();
        }

        $this->get('services.institution.inquiry')->updateInquiryStatus($inquiry, InstitutionInquiry::STATUS_DELETED);
        $responseText = array('status' => 1);
        


        $response = new Response(\json_encode($responseText), 200, array('content-type' => 'application/json'));

        return $response;
    
    }
    
    public function ajaxUpdateInquiriesStatusAction(Request $request)
    {
        $response = array('status' => 0);

        if($inquiryIds = $request->get('inquiry_ids')) {
            $institutionInquitryService = $this->get('services.institution.inquiry');
            $inquiries = $institutionInquitryService->updateInquiriesStatus($inquiryIds, $request->get('status'));
            $response['status'] = 1;
            $this->clearUnreadInquiriesSession();
        }

        $response = new Response(\json_encode($response), 200, array('content-type' => 'application/json'));
    
        return $response;
    }
    
    private function clearUnreadInquiriesSession()
    {
        $this->getRequest()->getSession()->set('unreadInquiries', null);
    }
}