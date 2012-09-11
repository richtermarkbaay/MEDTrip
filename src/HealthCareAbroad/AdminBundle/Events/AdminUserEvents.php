<?php
namespace HealthCareAbroad\AdminBundle\Events;

final class AdminUserEvents
{
    const ON_ADD_ADMIN_USER= 'event.admin_user.add';
    const ON_EDIT_ADMIN_USER = 'event.admin_user.edit';
    
    const ON_CHANGE_ADMIN_USER_PASSWORD = 'event.admin_user_password.change';
    const ON_DELETE_ADMIN_USER = 'event.admin_user.delete';
}
