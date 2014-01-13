<?php
namespace HealthCareAbroad\FrontendBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;

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

        //Falls through; order of the elements in $links is significant
        switch ($center->getPayingClient()) {
            case PayingStatus::PHOTO_LISTING:
            case PayingStatus::LOGO_LISTING:
            case PayingStatus::LINKED_LISTING:
                $socialMediaSites = json_decode($center->getSocialMediaSites(), true);
                foreach($socialMediaSites as $type => $value) {
                    if($value)
                        $links[$type] = array('tooltip' => "This hospital is on $value");
                }

                if ($website = $center->getWebsites()) {
                    $links['website'] = array('tooltip' => "Website: $website");
                }
                if ($number = $center->getContactNumber()) {
                    $links['contactnumber'] = array('tooltip' => 'Call Us', 'value' => $url);
                }
                break;
        }

        $links['email'] = array('tooltip' => 'Email Us', 'value' => $url.'#form_feedback');

        return $links;
    }

    /**
     * See comment in previous function
     */
    public function getInstitutionLinks(Institution $institution, $url)
    {
        $links = array();

        //Falls through; order of the elements in $links is significant
        if($institution->getPayingClient()) {
            $socialMediaSites = json_decode($institution->getSocialMediaSites(), true);
            foreach($socialMediaSites as $type => $value) {
                if($value) {
                    $links[$type] = array('tooltip' => "This hospital is on $value");                    
                }
            }

            if ($website = $institution->getWebsites()) {
                $links['website'] = array('tooltip' => "Website: $website");
            }

            if ($number = $institution->getContactNumber()) {
                $links['contactnumber'] = array('tooltip' => 'Call Us', 'value' => $url);
            }
        }

        $links['email'] = array('tooltip' => 'Email Us', 'value' => $url.'#form_feedback');

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
            $featuredMedia = $item->getFeaturedMedia();
            $links = $this->getInstitutionLinks($item, $url);

        } else {
            $url = $this->getInstitutionMedicalCenterFrontendUrl($item);
            $name = $item->getName();
            $description = strip_tags(strlen($item->getDescriptionHighlight()) ? $item->getDescriptionHighlight() : $item->getDescription());
            $supplementaryUrl = array('url' => $this->getInstitutionFrontendUrl($item->getInstitution()), 'name' => $item->getInstitution()->getName());
            $featuredMedia = $item->getInstitution()->getFeaturedMedia();
            $links = $this->getCenterLinks($item, $url);
        }

        $displayPhoto = $displayLogo = false;
        switch ($item->getPayingClient()) {
            case PayingStatus::PHOTO_LISTING:
                $displayPhoto = true;
            case PayingStatus::LOGO_LISTING:
                $displayLogo = true;
            case PayingStatus::LINKED_LISTING:
            case PayingStatus::FREE_LISTING:
        }

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
        $routeName = InstitutionService::getInstitutionRouteName($institution);

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

        return $this->router->generate('frontend_institutionMedicalCenter_profile', array(
            'institutionSlug' => $institutionSlug, 'imcSlug' => $imcSlug), true);
    }
}