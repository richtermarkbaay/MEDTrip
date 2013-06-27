<?php
namespace HealthCareAbroad\AdvertisementBundle\Twig;

use Symfony\Component\HttpFoundation\Session\Session;

use HealthCareAbroad\AdvertisementBundle\Services\Retriever;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;


class AdvertisementWidgetsTwigExtension extends \Twig_Extension
{   
    protected $twig;

    protected $retrieverService;
    
    /**
     * @var Session
     */
    protected $session;
    
    public function __construct(\Twig_Environment $twig, Retriever $retriever)
    {
        $this->twig = $twig;
        $this->retrieverService = $retriever;
    }
    
    public function setSessionService(Session $v)
    {
        $this->session = $v;
    }

    public function getFunctions()
    {
        return array(
            'render_homepage_premier_ad' => new \Twig_Function_Method($this, 'render_homepage_premier_ad'),
            'render_homepage_featured_clinics_ads' => new \Twig_Function_Method($this, 'render_homepage_featured_clinics_ads'),
            'render_homepage_featured_destinations_ads' => new \Twig_Function_Method($this, 'render_homepage_featured_destinations_ads'),
            'render_homepage_featured_posts_ads' => new \Twig_Function_Method($this, 'render_homepage_featured_posts_ads'),
            'render_homepage_common_treatments_ads' => new \Twig_Function_Method($this, 'render_homepage_common_treatments_ads'),
            'render_homepage_featured_video_ad' => new \Twig_Function_Method($this, 'render_homepage_featured_video_ad'),

            'render_search_results_featured_institution_ad' => new \Twig_Function_Method($this, 'render_search_results_featured_institution_ad'),
            'render_search_results_featured_clinic_ad' => new \Twig_Function_Method($this, 'render_search_results_featured_clinic_ad'),
            'render_search_results_image_ad' => new \Twig_Function_Method($this, 'render_search_results_image_ad'),
            'generate_ads_search_results_parameters_session_key' => new \Twig_Function_Method($this, 'generateSearchResultsParametersSessionKey'),
            'get_featured_institutions_by_search_parameters' => new \Twig_Function_Method($this, 'getFeaturedInstitutionsBySearchParameters'),
            'get_featured_clinics_by_search_parameters' => new \Twig_Function_Method($this, 'getFeaturedClinicsBySearchParameters'),
            'get_featured_institutions_session_key' => new \Twig_Function_Method($this, 'getFeaturedInstitutionsSessionKey'),
        );
    }


    public function render_homepage_premier_ad()
    {
        $this->twig->addGlobal('highlight', $this->retrieverService->getHomepagePremierAdvertisements());

        return $this->twig->display('AdvertisementBundle:Frontend:homepage.premierAdvertisements.html.twig');            
    }


    public function render_homepage_featured_clinics_ads()
    {
        $this->twig->addGlobal('featuredClinicsAds', $this->retrieverService->getHomepageFeaturedClinics());

        return $this->twig->display('AdvertisementBundle:Frontend:homepage.featuredClinics.html.twig');
    }


    public function render_homepage_featured_destinations_ads()
    {
        $this->twig->addGlobal('featuredDestinationsAds', $this->retrieverService->getHomepageFeaturedDestinations());

        return $this->twig->display('AdvertisementBundle:Frontend:homepage.featuredDestinations.html.twig');
    }


    public function render_homepage_featured_posts_ads()
    {
        $this->twig->addGlobal('featuredPostsAds', $this->retrieverService->getHomepageFeaturedPosts());
    
        return $this->twig->display('AdvertisementBundle:Frontend:homepage.featuredPosts.html.twig');
    }


    public function render_homepage_common_treatments_ads()
    {
        $this->twig->addGlobal('commonTreatmentsAds', $this->retrieverService->getHomepageCommonTreatments());
    
        return $this->twig->display('AdvertisementBundle:Frontend:homepage.commonTreatments.html.twig');
    }


    public function render_homepage_featured_video_ad()
    {
        $this->twig->addGlobal('featuredVideoAd', $this->retrieverService->getHomepageFeaturedVideo());
        
        return $this->twig->display('AdvertisementBundle:Frontend:homepage.featuredVideo.html.twig');
    }

    public function render_search_results_featured_institution_ad($params)
    {
        $ads = $this->retrieverService->getSearchResultsFeaturedInstitutionByCriteria($params);
        $this->twig->addGlobal('featuredAds', $ads);
        
        // added quick patch for filtering out displayed results items to exclude those institutions that are in featured ad
        $featuredInstitutionIds = array();
        foreach ($ads as $_ad) {
            $featuredInstitutionIds[] = $_ad->getInstitution()->getId();
        }
        // add the ids in current session. adding as global here won't work on already loaded twig templates
        // https://github.com/chromedia/healthcareabroad/issues/510
        $inSessionFeaturedInstitutions = $this->session->get($this->getFeaturedInstitutionsSessionKey());
        $inSessionFeaturedInstitutions[$this->generateSearchResultsParametersSessionKey($params)] = $featuredInstitutionIds;
        $this->session->set($this->getFeaturedInstitutionsSessionKey(), $inSessionFeaturedInstitutions);
        
        return $this->twig->display('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig');
    }

    public function render_search_results_featured_clinic_ad($params)
    {
        $ads = $this->retrieverService->getSearchResultsFeaturedClinicByCriteria($params);
        $this->twig->addGlobal('featuredAds', $ads);
        
        // https://github.com/chromedia/healthcareabroad/issues/510
        $featuredClinicIds = array();
        foreach ($ads as $_ad) {
            $featuredClinicIds[] = $_ad->getInstitutionMedicalCenter()->getId();
        }
        $inSession = $this->session->get($this->getFeaturedClinicsSessionKey());
        $inSession[$this->generateSearchResultsParametersSessionKey($params)] = $featuredClinicIds;
        $this->session->set($this->getFeaturedClinicsSessionKey(), $inSession);

        return $this->twig->display('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig');
    }


    public function render_search_results_image_ad($params = array())
    {
        $ads = $this->retrieverService->getSearchResultsImageAds($params);
        $this->twig->addGlobal('imageAds', $ads);

        return $this->twig->display('AdvertisementBundle:Frontend:imageAd.html.twig');
    }
    
    public function getName()
    {
        return 'advertisement_widgets_twig_extension';
    }
    
    public function getFeaturedInstitutionsBySearchParameters($params)
    {
        $inSessionFeaturedInstitutions =  $this->session->get($this->getFeaturedInstitutionsSessionKey());
        
        return $inSessionFeaturedInstitutions[$this->generateSearchResultsParametersSessionKey($params)];
    }
    
    public function getFeaturedInstitutionsSessionKey()
    {
        return 'searchResultsFeaturedInstitutions';
    }
    
    public function getFeaturedClinicsBySearchParameters($params)
    {
        $inSession = $this->session->get($this->getFeaturedClinicsSessionKey());
        
        return $inSession[$this->generateSearchResultsParametersSessionKey($params)];
    }
    
    public function getFeaturedClinicsSessionKey()
    {
        return 'searchResultsFeaturedClinics';
    }
    
    public function generateSearchResultsParametersSessionKey($params)
    {
        return \base64_encode(\serialize($params));
    }
}