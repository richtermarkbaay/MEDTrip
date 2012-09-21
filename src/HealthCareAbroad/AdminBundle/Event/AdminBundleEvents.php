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
    
    const ON_ADD_ADVERTISEMENT = 'event.advertisement.add';
    const ON_EDIT_ADVERTISEMENT = 'event.advertisement.edit';
    const ON_DELETE_ADVERTISEMENT = 'event.advertisement.delete';
    
    const ON_ADD_CITY = 'event.city.add';
    const ON_EDIT_CITY = 'event.city.edit';
    const ON_DELETE_CITY = 'event.city.delete';
    
    const ON_ADD_COUNTRY = 'event.country.add';
    const ON_EDIT_COUNTRY = 'event.country.edit';
    const ON_DELETE_COUNTRY = 'event.country.delete';
    
    const ON_ADD_MEDICAL_CENTER = 'event.medical_center.add';
    const ON_EDIT_MEDICAL_CENTER = 'event.medical_center.edit';
    const ON_DELETE_MEDICAL_CENTER = 'event.medical_center.delete';
    
    const ON_ADD_MEDICAL_PROCEDURE = 'event.medical_procedure.add';
    const ON_EDIT_MEDICAL_PROCEDURE = 'event.medical_procedure.edit';
    const ON_DELETE_MEDICAL_PROCEDURE = 'event.medical_procedure.delete';
    
    const ON_ADD_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.add';
    const ON_EDIT_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.edit';
    const ON_DELETE_MEDICAL_PROCEDURE_TYPE = 'event.medical_procedure_type.delete';
    
    const ON_ADD_NEWS = 'event.news.add';
    const ON_EDIT_NEWS = 'event.news.edit';
    const ON_DELETE_NEWS = 'event.news.delete';
    
    const ON_ADD_OFFERED_SERVICE = 'event.offered_service.add';
    const ON_EDIT_OFFERED_SERVICE = 'event.offered_service.edit';
    const ON_DELETE_OFFERED_SERVICE = 'event.offered_service.delete';
}