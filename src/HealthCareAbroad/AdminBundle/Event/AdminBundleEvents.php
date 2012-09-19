<?php
namespace HealthCareAbroad\AdminBundle\Event;

final class AdminBundleEvents
{
    const ON_ADD_ADMIN_USER = 'event.admin_user.add';
    const ON_EDIT_ADMIN_USER = 'event.admin_user.edit';
    const ON_LOGIN_ADMIN_USER = 'event.admin_user.login';
    const ON_CHANGE_PASSWORD_ADMIN_USER = 'event.admin_user.change_password';
}