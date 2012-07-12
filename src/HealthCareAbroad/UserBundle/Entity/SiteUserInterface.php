<?php
namespace HealthCareAbroad\UserBundle\Entity;


interface SiteUserInterface 
{
    public function getId();
    
    public function getEmail();
    
    public function getPassword();
        
}