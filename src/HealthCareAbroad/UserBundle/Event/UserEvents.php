<?php
namespace HealthCareAbroad\UserBundle\Event;

final class UserEvents
{
    const ON_CREATE_INSTITUTION_USER = 'event.institution_user.create';
    
    const ON_UPDATE_INSTITUTION_USER = 'event.institution_user.update';
    
    const ON_CHANGE_INSTITUTION_USER_PASSWORD = 'event.institution_user_password.change';
        
    const ON_DELETE_INSTITUTION_USER = 'event.institution_user.delete';
    
    
}