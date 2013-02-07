<?php

/**
 * Error Reports Controller
 * @author Chaztine Blance
 *
 */

namespace HealthCareAbroad\AdminBundle\Controller;

use Symfony\Component\Routing\Exception\MethodNotAllowedException;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use HealthCareAbroad\AdminBundle\Entity\ErrorReport;


class ErrorReportController extends Controller
{
    /**
     * Display Error Reports
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
    	return $this->render('AdminBundle:ErrorReport:index.html.twig', array(
            'reports' => $this->filteredResult,
            'pager' => $this->pager
        ));
    }
}