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


class FeedbackController extends Controller
{

    public function indexAction()
    {
    	return $this->render('AdminBundle:Feedback:index.html.twig', array(
            'feedback' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }
}