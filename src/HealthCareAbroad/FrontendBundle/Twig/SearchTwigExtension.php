<?php

namespace HealthCareAbroad\FrontendBundle\Twig;

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
            'get_center_links' => new \Twig_Function_Method($this, 'getCenterLinks')
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
    public function getCenterLinks(InstitutionMedicalCenter $center)
    {
        /*
  private 'contactNumber' => null
  private 'contactEmail' => string 'haroldmodest!@gmail.com' (length=23)
  private 'websites' => string 'http://google.com' (length=17)
  private 'websiteBackUp' => null
  private 'socialMediaSites' => null
  private 'isAlwaysOpen' => null
  private 'dateCreated' =>
    object(DateTime)[374]
      public 'date' => string '2013-07-30 12:19:39' (length=19)
      public 'timezone_type' => int 3
      public 'timezone' => string 'Europe/Berlin' (length=13)
  private 'dateUpdated' =>
    object(DateTime)[373]
      public 'date' => string '2013-07-30 12:19:39' (length=19)
      public 'timezone_type' => int 3
      public 'timezone' => string 'Europe/Berlin' (length=13)
  private 'isFromInternalAdmin' => int 1
  private 'status' => int 2
  private 'payingClient' => int 0
*/
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
                    $links['contactnumber'] = array('label' => 'Call Us', 'value' => $number);
                }
            case PayingStatus::FREE_LISTING:
                if ($email = $center->getContactEmail()) {
                    $links['email'] = array('label' => 'Email Us', 'value' => $email);
                }
        }

        return $links;
    }
}