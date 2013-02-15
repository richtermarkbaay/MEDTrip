<?php

namespace HealthCareAbroad\AdvertisementBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontendController extends Controller
{
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
}