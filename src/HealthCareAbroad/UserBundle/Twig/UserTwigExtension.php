<?php
namespace HealthCareAbroad\UserBundle\Twig;
use HealthCareAbroad\UserBundle\Services\UserService;

class UserTwigExtension extends \Twig_Extension
{
    /**
     * @var UserService
     */
    private $service;   
    
    public function getName()
    {
        return 'user';
    }
    
    public function setUserService(UserService $service)
    {
        $this->service = $service;
    }
    
    public function getFunctions()
    {
        return array('getAccountDataById' => new \Twig_Function_Method($this, 'getAccountDataById'));
    }
    
    public function getAccountDataById($id)
    {
        return $this->service->getAccountDataById($id);
    }
}