<?php
namespace HealthCareAbroad\HelperBundle\Entity;
/**
 * note: Helper Class for Social Media Sites
 * @author adelbertsilla
 *
 */
final class SocialMediaSites
{
    const FACEBOOK = 'facebook';
    const FACEBOOK_LABEL = 'Facebook';

    const TWITTER = 'twitter';
    const TWITTER_LABEL = 'Twitter';
    
    const GOOGLEPLUS = 'googleplus';
    const GOOGLEPLUS_LABEL = 'Google Plus';

    private static $types = array();
    private static $defaultValues = array();

    public static function getTypes()
    {
        return array(self::FACEBOOK, self::TWITTER, self::GOOGLEPLUS);
    }

    public static function getDefaultValues()
    {
        return array(self::FACEBOOK => '', self::TWITTER => '', self::GOOGLEPLUS => '');
    }

    public static function getLabels()
    {
        return array(
            self::FACEBOOK => self::FACEBOOK_LABEL,
            self::TWITTER => self::TWITTER_LABEL,
            self::GOOGLEPLUS => self::GOOGLEPLUS_LABEL
        );
    }

    public static function getPlaceHolders()
    {
        return array(
            self::FACEBOOK => 'Ex: https://www.facebook.com/healthcareabroadcom',
            self::TWITTER => 'Ex: https://twitter.com/HCAbroad',
            self::GOOGLEPLUS => 'Ex: https://plus.google.com/111671188601151092646'
        );
    }

    public static function getLabelByType($type)
    {
        $labels = self::getLabels();

        return $labels[$type];
    }

    public static function getPlaceHolderByType($type)
    {
        $placeholders = self::getPlaceHolders();

        return $placeholders[$type];
    }
}