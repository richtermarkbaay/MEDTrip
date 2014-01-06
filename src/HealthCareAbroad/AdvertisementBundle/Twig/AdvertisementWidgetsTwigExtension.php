<?php
namespace HealthCareAbroad\AdvertisementBundle\Twig;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeys;

use HealthCareAbroad\FrontendBundle\Services\FrontendMemcacheKeysHelper;

use HealthCareAbroad\MemcacheBundle\Services\MemcacheService;

use HealthCareAbroad\ApiBundle\Services\HcaBlogApiService;

use Symfony\Component\HttpFoundation\Session\Session;

use HealthCareAbroad\AdvertisementBundle\Services\Retriever;

use HealthCareAbroad\AdvertisementBundle\Entity\AdvertisementHighlightType;


class AdvertisementWidgetsTwigExtension extends \Twig_Extension
{   
    protected $twig;

    protected $retrieverService;
    
    protected $hcaBlogApiService;
    
    /**
     * @var Session
     */
    protected $session;
    
    /**
     * @var MemcacheService
     */
    protected $memcacheService;
    
    public function __construct(\Twig_Environment $twig, Retriever $retriever, HcaBlogApiService $hcaBlogApiService)
    {
        $this->twig = $twig;
        $this->retrieverService = $retriever;
        $this->hcaBlogApiService = $hcaBlogApiService;
    }
    
    public function setSessionService(Session $v)
    {
        $this->session = $v;
    }
    
    public function setMemcacheService(MemcacheService $memcache)
    {
        $this->memcacheService = $memcache;
    }

    public function getFunctions()
    {
        return array(
            'render_homepage_premier_ad' => new \Twig_Function_Method($this, 'renderHomepagePremierAds'),
            'render_homepage_featured_clinics_ads' => new \Twig_Function_Method($this, 'renderHomepageFeaturedClinicsAds'),
            'render_homepage_featured_destinations_ads' => new \Twig_Function_Method($this, 'renderHomepageFeaturedDestinationsAds'),
            'render_homepage_featured_posts_ads' => new \Twig_Function_Method($this, 'renderHomepageFeaturedPostsAds'),
            'render_homepage_common_treatments_ads' => new \Twig_Function_Method($this, 'renderHomepageCommonTreatmentsAds'),
            'render_homepage_featured_video_ad' => new \Twig_Function_Method($this, 'renderHomepageFeaturedVideoAd'),

            'render_search_results_featured_posts' => new \Twig_Function_Method($this, 'rendeSearchResultsFeaturedPosts'),
            'render_search_results_featured_institution_ad' => new \Twig_Function_Method($this, 'renderSearchResultsFeaturedInstitutionAd'),
            'render_search_results_featured_clinic_ad' => new \Twig_Function_Method($this, 'renderSearchResultsFeaturedClinicAd'),
            'render_search_results_image_ad' => new \Twig_Function_Method($this, 'renderSearchResultsImageAd'),
            'generate_ads_search_results_parameters_session_key' => new \Twig_Function_Method($this, 'generateSearchResultsParametersSessionKey'),
            'get_featured_institutions_by_search_parameters' => new \Twig_Function_Method($this, 'getFeaturedInstitutionsBySearchParameters'),
            'get_featured_clinics_by_search_parameters' => new \Twig_Function_Method($this, 'getFeaturedClinicsBySearchParameters'),
            'get_featured_institutions_session_key' => new \Twig_Function_Method($this, 'getFeaturedInstitutionsSessionKey'),
        );
    }


    public function renderHomepagePremierAds()
    {
        $ads = $this->retrieverService->getHomepagePremierAdvertisements();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.premierAdvertisements.html.twig', 
            array('highlight' => $ads)
        );  

        return $template;
    }


    public function renderHomepageFeaturedClinicsAds()
    {
        $ads = $this->retrieverService->getHomepageFeaturedClinics();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.featuredClinics.html.twig', array(
            'featuredClinicsAds' => $ads
        ));

        return $template;
    }


    public function renderHomepageFeaturedDestinationsAds()
    {
        $ads = $this->retrieverService->getHomepageFeaturedDestinations();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.featuredDestinations.html.twig', array(
            'featuredDestinationsAds' => $ads
        ));

        return $template;
    }


    public function renderHomepageFeaturedPostsAds()
    {
        $ads = $this->retrieverService->getHomepageFeaturedPosts();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.featuredPosts.html.twig', array(
            'featuredPostsAds' => $ads
        ));
        
        return $template;
    }


    public function renderHomepageCommonTreatmentsAds()
    {
        $ads = $this->retrieverService->getHomepageCommonTreatments();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.commonTreatments.html.twig', array(
            'commonTreatmentsAds' => $ads
        ));
        
        return $template;
    }


    public function renderHomepageFeaturedVideoAd()
    {
        $ads = $this->retrieverService->getHomepageFeaturedVideo();

        $template = $this->twig->render('AdvertisementBundle:Frontend:homepage.featuredVideo.html.twig', array(
            'featuredVideoAd' => $ads
        ));

        return $template;
    }

    public function rendeSearchResultsFeaturedPosts()
    {
        $memcacheKey = FrontendMemcacheKeys::SEARCH_RESULTS_BLOG_POSTS_ADS_KEY;
        $searchResultsFeaturedPosts = $this->memcacheService->get($memcacheKey);

        // Check if data is not yet Cached
        if(!($searchResultsFeaturedPosts)) {
            $searchResultsFeaturedPosts = $this->twig->render('AdvertisementBundle:Frontend:featuredPosts.html.twig', array(
                'featuredPosts' => $this->hcaBlogApiService->getBlogs())
            );
            $this->memcacheService->set($memcacheKey, $searchResultsFeaturedPosts);
        }

        return $searchResultsFeaturedPosts;
    }

    public function renderSearchResultsFeaturedInstitutionAd($params)
    {
        if(isset($params['cityId'])) {
            $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCityFeaturedAdsKey($params['cityId']);
        } else if(isset($params['countryId'])) {
            $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountryFeaturedAdsKey($params['countryId']);
        }

        $searchResultsFeaturedInstitutions = $this->memcacheService->get($memcacheKey);
        if(!$searchResultsFeaturedInstitutions) {

            if($ads = $this->retrieverService->getSearchResultsFeaturedInstitutionByCriteria($params)) {
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
                
                $searchResultsFeaturedInstitutions = $this->twig->render('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig', array('featuredAds' => $ads));
                
                $this->memcacheService->set($memcacheKey, $searchResultsFeaturedInstitutions);                
            }
        }

        return $searchResultsFeaturedInstitutions;
    }

    public function renderSearchResultsFeaturedClinicAd($params)
    {
        if(isset($params['treatmentId'])) {
            if(isset($params['cityId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCityTreatmentFeaturedAdsKey($params['cityId'], $params['treatmentId']);
            } elseif(isset($params['countryId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountryTreatmentFeaturedAdsKey($params['countryId'], $params['treatmentId']);
            } else {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsTreatmentFeaturedAdsKey($params['treatmentId']);
            }

        } else if(isset($params['subSpecializationId'])) {
            if(isset($params['cityId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCitySubSpecializationFeaturedAdsKey($params['cityId'], $params['subSpecializationId']);
            } elseif(isset($params['countryId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountrySubSpecializationFeaturedAdsKey($params['countryId'], $params['subSpecializationId']);
            } else {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsSubSpecializationFeaturedAdsKey($params['subSpecializationId']);
            }

        } else if(isset($params['specializationId'])) {
            if(isset($params['cityId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCitySpecializationFeaturedAdsKey($params['cityId'], $params['specializationId']);
            } elseif(isset($params['countryId'])) {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsCountrySpecializationFeaturedAdsKey($params['countryId'], $params['specializationId']);
            } else {
                $memcacheKey = FrontendMemcacheKeysHelper::generateSearchResultsSpecializationFeaturedAdsKey($params['specializationId']);
            }
        }

        $searchResultsFeaturedClinics = $this->memcacheService->get($memcacheKey);

        if(!$searchResultsFeaturedClinics) {
            if($ads = $this->retrieverService->getSearchResultsFeaturedClinicByCriteria($params)) {
                // https://github.com/chromedia/healthcareabroad/issues/510
                $featuredClinicIds = array();
                foreach ($ads as $ad) {
                    $featuredClinicIds[] = $ad->getInstitutionMedicalCenter()->getId();
                }
                $inSession = $this->session->get($this->getFeaturedClinicsSessionKey());
                $inSession[$this->generateSearchResultsParametersSessionKey($params)] = $featuredClinicIds;
                $this->session->set($this->getFeaturedClinicsSessionKey(), $inSession);
                
                $searchResultsFeaturedClinics = $this->twig->render('AdvertisementBundle:Frontend:searchResultsFeaturedAds.html.twig', array('featuredAds' => $ads));
                
                $this->memcacheService->set($memcacheKey, $searchResultsFeaturedClinics);                
            }
        }

        return $searchResultsFeaturedClinics;
    }


    public function renderSearchResultsImageAd($params = array())
    {
        $memcacheKey = FrontendMemcacheKeys::SEARCH_RESULTS_IMAGE_ADS_KEY;
        $imageAds = $this->memcacheService->get($memcacheKey);
        
        if(!$imageAds) {
            $ads = $this->retrieverService->getSearchResultsImageAds($params);
            $imageAds = $this->twig->render('AdvertisementBundle:Frontend:imageAd.html.twig', array('imageAds' => $ads));

            $this->memcacheService->set($memcacheKey, $imageAds);
        }

        return $imageAds; 
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