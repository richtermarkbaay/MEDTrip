<?php

namespace HealthCareAbroad\HelperBundle\Twig;

use HealthCareAbroad\InstitutionBundle\Entity\InstitutionInquiry;

use HealthCareAbroad\InstitutionBundle\Entity\Institution;

use Twig_Extension;
use Twig_Filter_Method;

use HealthCareAbroad\InstitutionBundle\Services\InstitutionService;
class TimeAgoTwigExtension extends \Twig_Extension
{
    protected $translator;

    public function getFunctions()
    {
        return array(
            'time_ago_in_words' => new \Twig_Function_Method($this, 'time_ago_in_words')
        );
    }

    public function time_ago_in_words(\DateTime $date)
    {
        
        $day = 60*60*24;
        $now = time();
        
        $fromTime = strtotime($date->format('Y-m-d'));
        
        $datediff = $now - $fromTime;
        $days =  floor($datediff/$day);
        if($days > 1) {
            $daysAgo = $days . " days ago";
        }
        elseif($days == 1) {
            $daysAgo = $days . " day ago";
        }
        else {
            $daysAgo = "today - " . $date->format('H:ma');
        }
        
        return $daysAgo;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'timeAgo';
    }
}