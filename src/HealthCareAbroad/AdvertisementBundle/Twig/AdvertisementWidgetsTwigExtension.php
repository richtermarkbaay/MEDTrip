<?php
namespace HealthCareAbroad\AdvertisementBundle\Twig;

use HealthCareAbroad\AdvertisementBundle\Services\Retriever;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;


class AdvertisementWidgetsTwigExtension extends \Twig_Extension
{   
    protected $twig;

    protected $retrieverService;
    
    public function __construct(\Twig_Environment $twig, Retriever $retriever)
    {
        $this->twig = $twig;
        $this->retrieverService = $retriever;
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
            'render_search_results_featured_clinic_ad' => new \Twig_Function_Method($this, 'render_search_results_featured_clinic_ad')
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

        return $this->twig->display('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig');
    }

    public function render_search_results_featured_clinic_ad($params)
    {
        $ads = $this->retrieverService->getSearchResultsFeaturedClinicByCriteria($params);
        $this->twig->addGlobal('featuredAds', $ads);

        return $this->twig->display('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig');
    }

    public function getName()
    {
        return 'advertisement_widgets_twig_extension';
    }
}