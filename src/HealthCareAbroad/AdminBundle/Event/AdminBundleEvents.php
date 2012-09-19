<?php
namespace HealthCareAbroad\AdminBundle\Event;

/**
 * List of constant values for events dispatched in AdminBundle.
 * 
 * @author Allejo Chris G. Velarde
 */
final class AdminBundleEvents
{
    const ON_ADD_ADMIN_USER = 'event.admin_user.add';
    const ON_EDIT_ADMIN_USER = 'event.admin_user.edit';
    const ON_LOGIN_ADMIN_USER = 'event.admin_user.login';
    const ON_CHANGE_PASSWORD_ADMIN_USER = 'event.admin_user.change_password';
    
    const ON_ADD_ADMIN_USER_TYPE = 'event.admin_user_type.add';
    const ON_EDIT_ADMIN_USER_TYPE = 'event.admin_user_type.edit';
    const ON_DELETE_ADMIN_USER_TYPE = 'event.admin_user_type.delete';
    const ON_ADD_ADMIN_USER_TYPE_ROLE = 'event.admin_user_type.add_role';
    const ON_DELETE_ADMIN_USER_TYPE_ROLE = 'event.admin_user_type.delete_role';
}