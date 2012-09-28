<?php
namespace HealthCareAbroad\MediaBundle\Resizer;

use Gaufrette\File;
use HealthCareAbroad\MediaBundle\Entity\Media;

interface Resizer
{
    /**
     * @param Media $media
     * @param File $in
     * @param File $out
     * @param string $format
     * @param array $settings
     */
    function resize(Media $media, File $in, File $out, $format, array $settings);

    /**
     * @param Media $media
     * @param array $settings
     */
    function getBox(Media $media, array $settings);
}