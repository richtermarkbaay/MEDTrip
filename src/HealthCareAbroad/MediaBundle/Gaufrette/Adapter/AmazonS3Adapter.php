<?php
namespace HealthCareAbroad\MediaBundle\Gaufrette\Adapter;

use Gaufrette\Adapter\AmazonS3;

class AmazonS3Adapter extends AmazonS3
{
    function getUrl($isSecured = true)
    {
        $protocol = $isSecured ? 'https' : 'http';

        return "$protocol://" . $this->options['region'] . '/' . $this->bucket . '/' . $this->options['directory']; 
    }    
}