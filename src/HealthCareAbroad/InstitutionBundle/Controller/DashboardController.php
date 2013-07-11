<?php

namespace HealthCareAbroad\InstitutionBundle\Controller;

use Symfony\Component\HttpKernel\Exception\HttpException;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\InstitutionBundle\Form\InstitutionProfileFormType;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenterStatus;

use HealthCareAbroad\PagerBundle\Adapter\DoctrineOrmAdapter;

use HealthCareAbroad\PagerBundle\Pager;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

class DashboardController extends InstitutionAwareController
{
    /**
     * @var InstitutionMedicalCenterRepository
     */
    private $repository;
    
    /**
     * @var InstitutionMedicalCenterService
     */
    private $service;
    
    public $institutionMedicalCenter;
    
    
    public function indexAction(Request $request)
    {
        //$institutionAlerts = $this->container->get('services.alert')->getAlertsByInstitution($this->institution);
        $institutionAlerts = array();

        if($request->server->has('HTTP_REFERER')){
            if (\preg_match('/setup-doctors/i', $request->server->get('HTTP_REFERER'))) {
                $newlySignedup = true;
            }
        }

        // TODO - Deprecated??
        //$newsRepository = $this->getDoctrine()->getRepository('HelperBundle:News');
        //$news = $newsRepository->getLatestNews();
        $news = array();

        return $this->render('InstitutionBundle:Dashboard:index.html.twig', array(
            'alerts' => $institutionAlerts,
            'news' => $news,
            'institution' => $this->institution,
            'newlySignedup' => isset($newlySignedup) ? true : false
        ));
    }
    
	public function mediaAjaxDelete()
	{
	    
	}
}
