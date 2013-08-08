<?php
namespace HealthCareAbroad\HelperBundle\Entity;

final class SocialMediaSites
{
    const SCHEME = 'https://'; 
    
    const FACEBOOK = 'facebook';
    const FACEBOOK_LABEL = 'Facebook';
    const FACEBOOK_DOMAIN = 'facebook.com';

    const TWITTER = 'twitter';
    const TWITTER_LABEL = 'Twitter';
    const TWITTER_DOMAIN = 'twitter.com';

    const GOOGLEPLUS = 'googleplus';
    const GOOGLEPLUS_LABEL = 'Google Plus';
    const GOOGLEPLUS_DOMAIN = 'plus.google.com';

    private static $types = array();
    private static $urls = array();
    private static $defaultValues = array();


    public static function getTypes()
    {
        return array(self::FACEBOOK, self::TWITTER, self::GOOGLEPLUS);
    }
    
    public static function getDefaultValues()
    {
        return array(self::FACEBOOK => '', self::TWITTER => '', self::GOOGLEPLUS => '');
    }

    public static function getUrls()
    {
        return array(
            self::FACEBOOK => self::SCHEME . self::FACEBOOK_DOMAIN . '/',
            self::TWITTER => self::SCHEME . self::TWITTER_DOMAIN . '/',
            self::GOOGLEPLUS => self::SCHEME . self::GOOGLEPLUS_DOMAIN . '/'
        );
    }
    
    public static function getLabels()
    {
        return array(
            self::FACEBOOK => self::FACEBOOK_LABEL,
            self::TWITTER => self::TWITTER_LABEL,
            self::GOOGLEPLUS => self::GOOGLEPLUS_LABEL
        );
    }
    
    public static function getUrlByType($type)
    {
        $urls = self::getUrls();

        return $urls[$type];
    }
    
    public static function getLabelByType($type)
    {
        $labels = self::getLabels();
    
        return $labels[$type];
    }

    public static function formatSites($jsonString)
    {
        $urls = self::getUrls();
        $labels = self::getLabels();
        $arrSiteValues = \json_decode($jsonString, true);
        $returnValues = array();
        
        foreach($urls as $type => $url) {
            $value = isset($arrSiteValues[$type]) && $arrSiteValues[$type] ? $url . $arrSiteValues[$type] : '';

            $returnValues[$type] = array('label' => $labels[$type], 'value' => $value);
        }

        return $returnValues;
    }
}