<?php
namespace HealthCareAbroad\UserBundle\Event;

final class UserEvents
{
    const ON_CREATE_INSTITUTION_USER = 'event.institution_user.create';
    
    const ON_UPDATE_INSTITUTION_USER = 'event.institution_user.update';
    
    const ON_CHANGE_INSTITUTION_USER_PASSWORD = 'event.institution_user.change_password';
    
    const ON_LOGIN_INSTITUTION_USER = 'event.institution_user.login';
    
    const ON_LOGOUT_INSTITUTION_USER = 'event.institution_user.logout';
        
    const ON_DELETE_INSTITUTION_USER = 'event.institution_user.delete';
    
    
    
    
}