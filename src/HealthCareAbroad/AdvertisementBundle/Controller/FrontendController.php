<?php

namespace HealthCareAbroad\AdvertisementBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
    public function renderHomepagePremierAdvertisementsAction()
    {
        $advertisements = $this->get('services.advertisement.retriever')->getHomepageAdvertisementByType(1);
        
        return $this->render('AdvertisementBundle:Frontend:homepage.premierAdvertisements.html.twig', array(
            'highlight' => \count($advertisements) ? $advertisements[array_rand($advertisements)] : null,
        ));
    }
    
    /**
     * Only accessible through Twig render
     */
    public function renderFeaturedHomepageVideoAction()
    {
        $advertisements = $this->get('services.advertisement.retriever')->getHomepageAdvertisementByType(5);
        
        if (!\count($advertisements)) {
            return new Response('', 200);
        }
        
        // only one
        $advertisement = $advertisements[0];
        
        \preg_match('/[\\?\\&]v=([^\\?\\&]+)/',$advertisement->getVideoUrl(),$matches);
        
        return $this->render('AdvertisementBundle:Frontend:homepage.featuredVideo.html.twig', array(
            'advertisement' => $advertisement,
            'featuredVideo' => array(
                                'youtubeId' => $matches[1]
                            )
        ));
    }
    
    public function renderHomepageFeaturedPostsAction()
    {
        $advertisements = $this->get('services.advertisement.retriever')->getHomepageAdvertisementByType(6);
        
        return $this->render('AdvertisementBundle:Frontend:homepage.featuredPosts.html.twig', array(
                        'advertisements' => $advertisements,
        ));
    }
    
    public function renderHomepageCommonTreatmentsAction()
    {
        $advertisements = $this->get('services.advertisement.retriever')->getHomepageCommonTreatments();
        
        return $this->render('AdvertisementBundle:Frontend:homepage.commonTreatments.html.twig', array(
                        'advertisements' => $advertisements,
        ));
    }
    
    public function renderHomepageFeaturedClinicsAction()
    {
        $advertisements = $this->get('services.advertisement.retriever')->getHomepageFeaturedClinics();
        
        return $this->render('AdvertisementBundle:Frontend:homepage.featuredClinics.html.twig', array(
                        'advertisements' => $advertisements,
        ));
    }
}