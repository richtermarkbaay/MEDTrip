<?php
/**
 * Frontend controller for advertisements
 * 
 * @author Allejo Chris G. Velarde
 */

namespace HealthCareAbroad\FrontendBundle\Controller;

use HealthCareAbroad\AdvertisementBundle\Services\AdvertisementService;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdvertisementController extends Controller
{
    /**
     * @var AdvertisementService
     */
    private $service;
    
    /**
     * Controller for showing widget for featured institutions
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeaturedInstitutionAdvertisementAction(Request $request)
    {
        $this->service = $this->get('services.advertisement');
        $advertisements = $this->service->getActiveFeaturedInstitutionAdvertisements();
        
        return $this->render('FrontendBundle:Advertisement:featuredInstitutions.html.twig', array('advertisements' => $advertisements));
    }
    
    /**
     * Controller for showing widget for featured listing
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showFeaturedListingAdvertisementAction(Request $request)
    {
        $this->service = $this->get('services.advertisement');
        $advertisements = $this->service->getActiveFeaturedListingAdvertisements();
        
        return $this->render('FrontendBundle:Advertisement:featuredListings.html.twig', array('advertisements' => $advertisements));
    }
    
    /**
     * Controller for showing widget for news ticker
     * 
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showNewsTickerAdvertisementAction(Request $request)
    {
        $this->service = $this->get('services.advertisement');
        $advertisements = $this->service->getActiveNewsTickerAdvertisements();
        
        return $this->render('FrontendBundle:Advertisement:newsTickers.html.twig', array('advertisements' => $advertisements));
    }
}