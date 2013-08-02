<?php 

/**
 * @author Adelbert D. Silla
 * 
 * Image Sizes
 * 
 */

namespace HealthCareAbroad\MediaBundle\Services;

final class ImageSizes {

    const MINI = '55x55';
    
    const SMALL = '100x100';
    
    const MEDIUM = '135x135';
    
    const MEDIUM_BANNER = '620x258';

    const LARGE_BANNER = '950x420';
    
    const GALLERY_LARGE_THUMBNAIL = '260x150';

    const ADS_FEATURED_IMAGE = '135x100';
    
    const DOCTOR_LOGO = '61x61';
    
    const SPECIALIZATION_DEFAULT_LOGO = '83x83';


    static function getWidth($size)
    {
        $size = self::toArray($size);
        
        return $size['width'];
    }

    static function getHeight($size)
    {
        $size = self::toArray($size);
        
        return $size['height'];
    }

    static function toArray($size)
    {
        $size = explode('x', $size);
        
        return array('width' => $size[0], 'height' => $size[1]);
    }
}