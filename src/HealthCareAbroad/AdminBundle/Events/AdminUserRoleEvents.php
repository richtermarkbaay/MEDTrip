<?php
namespace HealthCareAbroad\AdminBundle\Events;

final class AdminUserRoleEvents
{
    const ON_ADD_ADMIN_USER_ROLE= 'event.admin_user_role.add';
    const ON_EDIT_ADMIN_USER_ROLE = 'event.admin_user_role.edit';
    const ON_DELETE_ADMIN_USER_ROLE = 'event.admin_user_role.delete';
}