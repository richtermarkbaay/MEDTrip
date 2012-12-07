<?php 
namespace HealthCareAbroad\HelperBundle\Entity;

final Class RouteType
{
    const LOGIN = 'institution_signUp';

    const SIGNUP = 'institution_login';
    
    static public function getFormChoicesLabel()
    {
        return array(
                        self::LOGIN => 'Signup',
                        self::SIGNUP => 'Login'
        );
    }
}