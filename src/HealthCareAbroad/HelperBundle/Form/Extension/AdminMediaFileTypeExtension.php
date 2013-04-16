<?php
namespace HealthCareAbroad\HelperBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;

class AdminMediaFileTypeExtension extends AbstractTypeExtension
{
    public function getExtendedType()
    {
        return 'admin_media_file';
    }
}