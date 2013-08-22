<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionTypes;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;
use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;
use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;
use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SearchTwigExtension extends \Twig_Extension
{
    private $router;
    private $institutionService;

    public function getFunctions()
    {
        return array(
            'get_narrow_search_widgets_configuration' => new \Twig_Function_Method($this, 'get_narrow_search_widgets_configuration'),
            'get_center_links' => new \Twig_Function_Method($this, 'getCenterLinks'),
            'get_institution_links' => new \Twig_Function_Method($this, 'getInstitutionLinks'),
            'get_search_result_item' => new \Twig_Function_Method($this, 'getSearchResultItem')
        );
    }

    public function getName()
    {
        return 'frontendSearchExtension';
    }

    public function setRouter($v)
    {
        $this->router = $v;
    }

    public function setInstitutionService($service)
    {
        $this->institutionService = $service;
    }

    public function get_narrow_search_widgets_configuration($widgets, $commonAutocompleteOptions=array())
    {
        $grouping = array(
            'treatments' => array('specialization', 'treatment', 'sub_specialization'),
            'destinations' => array('country','city')
        );
        $availableWidgetsConfiguration = array(
            'specialization' => array(
                'type' => 'specialization',
                'widget_container' => 'li.narrow_search_widget_specialization',
                'autocomplete' => $commonAutocompleteOptions
            ),
            'sub_specialization' => array(
                'type' => 'sub-specialization',
                'widget_container' => 'li.narrow_search_widget_sub-specialization',
                'autocomplete' => $commonAutocompleteOptions
            ),
            'treatment' => array(
                'type' => 'treatment',
                'widget_container' => 'li.narrow_search_widget_treatment',
                'autocomplete' => $commonAutocompleteOptions
            ),
            'country' => array(
                'type' => 'country',
                'widget_container' => 'li.narrow_search_widget_country',
                'autocomplete' => $commonAutocompleteOptions
            ),
            'city' => array(
                'type' => 'city',
                'widget_container' => 'li.narrow_search_widget_city',
                'autocomplete' => $commonAutocompleteOptions
            )
        );

        $widgetConfigurations = \array_intersect_key($availableWidgetsConfiguration, \array_flip($widgets));

//         if ($groupedByType) {
//             $groupedWidgets = array('treatments' => array(), 'destinations' => array());
//             foreach ($widgetConfigurations as $widgetKey => $conf) {
//                 if (\in_array($widgetKey, $grouping['treatments'])) {
//                     $groupedWidgets['treatments'][] = $conf;
//                 }
//                 elseif(\in_array($widgetKey, $grouping['destinations'])) {
//                     $groupedWidgets['destinations'][] = $conf;
//                 }
//             }
//             $widgetConfigurations = $groupedWidgets;
//         }

        return $widgetConfigurations;
    }

    /**
     * This will group together all relevant links - clinic website, social media
     * sites, email, call us, etc in one variable. Its purpose is to make it easier
     * on the view side to display these links.
     *
     * NOTE: It is imperative that this return only the permitted links for each
     * paying status type. There will be no checking on the view side.
     *
     * Rules are here: https://basecamp.com/1876919/projects/3113924-health-care-abroad/todos/55796212-add-interface-to
     *
     */
    public function getCenterLinks(InstitutionMedicalCenter $center, $url)
    {
        $links = array();
        //TODO: fix formatSites so that it can accept strings with or without the
        //social media site's prefix: can accept either "https://facebook.com/my_name" or just "my_name"
        //$socialMediaSites = SocialMediaSites::formatSites($center->getSocialMediaSites());
        $socialMediaSites = json_decode($center->getSocialMediaSites(), true);

        //Falls through; order of the elements in $links is significant
        switch ($center->getPayingClient()) {
            case PayingStatus::PHOTO_LISTING:
            case PayingStatus::LOGO_LISTING:
            case PayingStatus::LINKED_LISTING:
                if (isset($socialMediaSites[SocialMediaSites::GOOGLEPLUS]) && $socialMediaSites[SocialMediaSites::GOOGLEPLUS]) {
                    $links[SocialMediaSites::GOOGLEPLUS] = array('label' => 'Visit G+', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::GOOGLEPLUS], true));
                }
                if (isset($socialMediaSites[SocialMediaSites::TWITTER]) && $socialMediaSites[SocialMediaSites::TWITTER]) {
                    $links[SocialMediaSites::TWITTER] = array('label' => 'Visit Twitter', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::TWITTER], true));
                }
                if (isset($socialMediaSites[SocialMediaSites::FACEBOOK]) && $socialMediaSites[SocialMediaSites::FACEBOOK]) {
                    $links[SocialMediaSites::FACEBOOK] = array('label' => 'Visit Facebook', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::FACEBOOK], true));
                }
                if ($website = $center->getWebsites()) {
                    $links['website'] = array('label' => 'Visit Website', 'value' => $this->appendScheme($website));
                }
                if ($number = $center->getContactNumber()) {
                    $links['contactnumber'] = array('label' => 'Call Us', 'value' => $url);
                }
            case PayingStatus::FREE_LISTING:
                if ($email = $center->getContactEmail()) {
                    $links['email'] = array('label' => 'Email Us', 'value' => $url.'#form_feedback');
                }
        }

        return $links;
    }

    /**
     * See comment in previous function
     */
    public function getInstitutionLinks(Institution $institution, $url)
    {
        $links = array();
        //TODO: fix formatSites so that it can accept strings with or without the social media
        // site's prefix: e.g. can accept either "https://facebook.com/my_name" or just "my_name"
        //$socialMediaSites = SocialMediaSites::formatSites($institution->getSocialMediaSites());
        $socialMediaSites = json_decode($institution->getSocialMediaSites(), true);

        //Falls through; order of the elements in $links is significant
        switch ($institution->getPayingClient()) {
            case 1:
                if (isset($socialMediaSites[SocialMediaSites::GOOGLEPLUS])) {
                    $links[SocialMediaSites::GOOGLEPLUS] = array('label' => 'Visit G+', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::GOOGLEPLUS], true));
                }
                if (isset($socialMediaSites[SocialMediaSites::TWITTER])) {
                    $links[SocialMediaSites::TWITTER] = array('label' => 'Visit Twitter', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::TWITTER], true));
                }
                if (isset($socialMediaSites[SocialMediaSites::FACEBOOK])) {
                    $links[SocialMediaSites::FACEBOOK] = array('label' => 'Visit Facebook', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::FACEBOOK], true));
                }
                if ($website = $institution->getWebsites()) {
                    $links['website'] = array('label' => 'Visit Website', 'value' => $this->appendScheme($website));
                }
                if ($number = $institution->getContactNumber()) {
                    $links['contactnumber'] = array('label' => 'Call Us', 'value' => $url);
                }
            case 0:
                if ($email = $institution->getContactEmail()) {
                    $links['email'] = array('label' => 'Email Us', 'value' => $url.'#form_feedback');
                }
        }

        return $links;
    }

    /**
     * Support only objects for now. Item is either an instance of Institution
     * or InstitutionMedicalCenter
     *
     * @param unknown $item
     * @return multitype:string
     */
    public function getSearchResultItem($item)
    {
        $isInstitution = $item instanceof Institution;
        if ($item instanceof InstitutionMedicalCenter) {
            $isInstitution = $item->getInstitution()->getType() == InstitutionTypes::SINGLE_CENTER;
            if ($isInstitution) {
                $item = $item->getInstitution();
            }
        }

        if ($isInstitution) {
            $url = $this->getInstitutionFrontendUrl($item);
            $name = $item->getName();
            $description = strip_tags($item->getDescription());
            $supplementaryUrl = false;
            $displayPhoto = $displayLogo = $item->getPayingClient() == 1;
            $featuredMedia = $item->getFeaturedMedia();
            $links = $this->getInstitutionLinks($item, $url);

        } else {
            $url = $this->getInstitutionMedicalCenterFrontendUrl($item);
            $name = $item->getName();
            $description = strip_tags(strlen($item->getDescriptionHighlight()) ? $item->getDescriptionHighlight() : $item->getDescription());
            $supplementaryUrl = array('url' => $this->getInstitutionFrontendUrl($item->getInstitution()), $name => $item->getInstitution()->getName());
            switch ($item->getPayingClient()) {
                case PayingStatus::PHOTO_LISTING:
                    $displayPhoto = true;
                case PayingStatus::LOGO_LISTING:
                    $displayLogo = true;
                case PayingStatus::LINKED_LISTING:
                case PayingStatus::FREE_LISTING:
            }
            $featuredMedia = $item->getInstitution()->getFeaturedMedia();
            $links = $this->getCenterLinks($item, $url);
        }

        /*
            hideSubLink: hideSubLink,
            logo: render_institution_medical_center_logo(center, {attr: {class: 'small', alt: 'clinic logo'}}),
            institutionName: center.institution.name,
            mainAddress: medical_center_complete_address_to_string(center, ['city', 'state', 'country']),
            streetAddress: medical_center_complete_address_to_string(center, ['address', 'zipCode']),

            mainAddress: institution_address_to_string(institution, ['city', 'state', 'country']),
            streetAddress: institution_address_to_string(institution, ['address1', 'zipCode']),

        */

        return array(
            'isInstitution' => $isInstitution,
            'url' => $url,
            'name' => $name,
            'supplementaryUrl' => $supplementaryUrl,
            'description' => $description,
            'displayPhoto' => $displayPhoto,
            'displayLogo' => $displayLogo,
            'featuredMedia' => $featuredMedia,
            'links' => $links,
            'dataObject' => $item
        );
    }

    private function getInstitutionFrontendUrl($institution)
    {
        $slug = $institution instanceof Institution ? $institution->getSlug() : $institution['slug'];
        $routeName = $this->institutionService->getInstitutionRouteName($institution);

        return $this->router->generate($routeName, array('institutionSlug' => $slug), true);
    }

    // Calling code will already check the institution type so most of the code for this
    // method can be removed.
    private function getInstitutionMedicalCenterFrontendUrl($center)
    {
        if ($center instanceof InstitutionMedicalCenter) {
            $institution = $center->getInstitution();
            $institutionType = $institution->getType();
            $institutionSlug = $institution->getSlug();
            $imcSlug = $center->getSlug();
        } else {
            $institution = $center['institution'];
            $institutionType = $institution['type'];
            $institutionSlug = $institution['slug'];
            $imcSlug = $center['slug'];
        }

        if (InstitutionTypes::SINGLE_CENTER == $institutionType) {
            return $this->getInstitutionFrontendUrl($institution);
        }

        return $this->router->generate('frontend_institutionMedicaCenter_profile', array(
            'institutionSlug' => $institutionSlug, 'imcSlug' => $imcSlug));
    }

    /**
     * FIXME: this sidesteps the real issue (?) which is that urls in the template
     * are prepended with the current page's uri if scheme is absent.
     *
     * @param unknown $uri
     * @param string $prefix
     */
    private function appendScheme($uri, $isSecureScheme = false)
    {
        $scheme = 'http';
        $pattern = "/^({$scheme})(s?)(:\/\/.*$)/";

        if (preg_match($pattern, $uri, $matches) === 0) {
            $uri = $scheme . ($isSecureScheme ? 's://' : '://') . $uri;
        } else {
            if ($isSecureScheme && !$matches[2]) {
                $uri = $matches[1].'s'.$matches[3];
            }
        }

        return $uri;
    }
}