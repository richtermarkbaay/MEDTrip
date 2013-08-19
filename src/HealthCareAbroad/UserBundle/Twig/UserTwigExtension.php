<?php
namespace HealthCareAbroad\UserBundle\Twig;
use Symfony\Component\Security\Core\User\UserInterface;

use HealthCareAbroad\UserBundle\Entity\AdminUser;

use HealthCareAbroad\UserBundle\Entity\SiteUser;

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
        return array(
            'getAccountDataById' => new \Twig_Function_Method($this, 'getAccountDataById'),
            'is_internal_admin_user' => new \Twig_Function_Method($this, 'is_admin_user')
        );
    }
    
    public function is_admin_user(UserInterface $user)
    {
        return $user instanceof AdminUser;
    }
    
    public function getAccountDataById($id)
    {
        return $this->service->getAccountDataById($id);
    }
}