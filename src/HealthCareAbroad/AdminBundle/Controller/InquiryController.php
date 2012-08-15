<?php

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use HealthCareAbroad\AdminBundle\Entity\Inquiry;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class InquiryController extends Controller
{

    /**
     * @PreAuthorize("hasAnyRole('SUPER_ADMIN', 'CAN_VIEW_INQUIRIES')")
     */
    public function indexAction()
    {
		$inquiries = $this->getDoctrine()->getEntityManager()->getRepository('AdminBundle:Inquiry')->findAll();
    	$data = array('inquiries'=>$inquiries);
    	return $this->render('AdminBundle:Inquiry:index.html.twig', $data);
    }
}