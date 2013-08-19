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
        $socialMediaSites = SocialMediaSites::formatSites($center->getSocialMediaSites());

        //Falls through; order of the elements in $links is significant
        switch ($center->getPayingClient()) {
            case PayingStatus::PHOTO_LISTING:
            case PayingStatus::LOGO_LISTING:
            case PayingStatus::LINKED_LISTING:
                if (isset($socialMediaSites['googleplus'])) {
                    $links[SocialMediaSites::GOOGLEPLUS] = array('label' => 'Visit G+', 'value' => $socialMediaSites[SocialMediaSites::GOOGLEPLUS]['value']);
                }
                if (isset($socialMediaSites[SocialMediaSites::TWITTER])) {
                    $links[SocialMediaSites::TWITTER] = array('label' => 'Visit Twitter', 'value' => $socialMediaSites[SocialMediaSites::TWITTER]['value']);
                }
                if (isset($socialMediaSites[SocialMediaSites::FACEBOOK])) {
                    $links[SocialMediaSites::FACEBOOK] = array('label' => 'Visit Facebook', 'value' => $socialMediaSites[SocialMediaSites::FACEBOOK]['value']);
                }
                if ($website = $center->getWebsites()) {
                    $links['website'] = array('label' => 'Visit Website', 'value' => $website);
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
        $socialMediaSites = SocialMediaSites::formatSites($institution->getSocialMediaSites());

        //Falls through; order of the elements in $links is significant
        switch ($institution->getPayingClient()) {
            case 1:
                if (isset($socialMediaSites['googleplus'])) {
                    $links[SocialMediaSites::GOOGLEPLUS] = array('label' => 'Visit G+', 'value' => $socialMediaSites[SocialMediaSites::GOOGLEPLUS]['value']);
                }
                if (isset($socialMediaSites[SocialMediaSites::TWITTER])) {
                    $links[SocialMediaSites::TWITTER] = array('label' => 'Visit Twitter', 'value' => $socialMediaSites[SocialMediaSites::TWITTER]['value']);
                }
                if (isset($socialMediaSites[SocialMediaSites::FACEBOOK])) {
                    $links[SocialMediaSites::FACEBOOK] = array('label' => 'Visit Facebook', 'value' => $socialMediaSites[SocialMediaSites::FACEBOOK]['value']);
                }
                if ($website = $institution->getWebsites()) {
                    $links['website'] = array('label' => 'Visit Website', 'value' => $website);
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
}