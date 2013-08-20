<?php

namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use HealthCareAbroad\HelperBundle\Entity\SocialMediaSites;

use HealthCareAbroad\InstitutionBundle\Entity\PayingStatus;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionMedicalCenter;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

class SearchTwigExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    public function getFunctions()
    {
        return array(
            'get_narrow_search_widgets_configuration' => new \Twig_Function_Method($this, 'get_narrow_search_widgets_configuration'),
            'get_center_links' => new \Twig_Function_Method($this, 'getCenterLinks'),
            'get_institution_links' => new \Twig_Function_Method($this, 'getInstitutionLinks'),
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
                    $links[SocialMediaSites::GOOGLEPLUS] = array('label' => 'Visit G+', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::GOOGLEPLUS], $true));
                }
                if (isset($socialMediaSites[SocialMediaSites::TWITTER])) {
                    $links[SocialMediaSites::TWITTER] = array('label' => 'Visit Twitter', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::TWITTER], $true));
                }
                if (isset($socialMediaSites[SocialMediaSites::FACEBOOK])) {
                    $links[SocialMediaSites::FACEBOOK] = array('label' => 'Visit Facebook', 'value' => $this->appendScheme($socialMediaSites[SocialMediaSites::FACEBOOK], $true));
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