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
    
    const ON_ADMIN_MIGRATE_SPECIALIZATION = 'event.admin.migrate_specialization';
    const ON_ADMIN_MERGE_TREATMENT = 'event.admin.merge_treatment';

    const ON_ADD_ADVERTISEMENT = 'event.advertisement.add';
    const ON_EDIT_ADVERTISEMENT = 'event.advertisement.edit';
    const ON_DELETE_ADVERTISEMENT = 'event.advertisement.delete';

    const ON_ADD_CITY = 'event.city.add';
    const ON_EDIT_CITY = 'event.city.edit';
    const ON_DELETE_CITY = 'event.city.delete';
    
    const ON_ADD_STATE = 'event.state.add';
    const ON_EDIT_STATE = 'event.state.edit';
    const ON_DELETE_STATE = 'event.state.delete';

    const ON_ADD_COUNTRY = 'event.country.add';
    const ON_EDIT_COUNTRY = 'event.country.edit';
    const ON_DELETE_COUNTRY = 'event.country.delete';

    const ON_ADD_SPECIALIZATION = 'event.specialization.add';
    const ON_EDIT_SPECIALIZATION = 'event.specialization.edit';
    const ON_DELETE_SPECIALIZATION = 'event.specialization.delete';

    const ON_ADD_TREATMENT = 'event.treatment.add';
    const ON_EDIT_TREATMENT = 'event.treatment.edit';
    const ON_DELETE_TREATMENT = 'event.treatment.delete';

    const ON_ADD_SUB_SPECIALIZATION = 'event.subspecialization.add';
    const ON_EDIT_SUB_SPECIALIZATION = 'event.subspecialization.edit';
    const ON_DELETE_SUB_SPECIALIZATION = 'event.subspecialization.delete';

    const ON_ADD_NEWS = 'event.news.add';
    const ON_EDIT_NEWS = 'event.news.edit';
    const ON_DELETE_NEWS = 'event.news.delete';

    const ON_ADD_OFFERED_SERVICE = 'event.offered_service.add';
    const ON_EDIT_OFFERED_SERVICE = 'event.offered_service.edit';
    const ON_DELETE_OFFERED_SERVICE = 'event.offered_service.delete';

    const ON_ADD_LANGUAGE = 'event.language.add';
    const ON_EDIT_LANGUAGE = 'event.language.edit';
    const ON_DELETE_LANGUAGE = 'event.language.delete';
    
    const ON_ADD_AWARDING_BODY = 'event.awarding_body.add';
    const ON_EDIT_AWARDING_BODY = 'event.awarding_body.edit';
    const ON_DELETE_AWARDING_BODY = 'event.awarding_body.delete';
    
    const ON_ADD_AFFILIATION = 'event.global_award.add';
    const ON_EDIT_AFFILIATION = 'event.global_award.edit';
    const ON_DELETE_AFFILIATION = 'event.global_award.delete';
    
    const ON_ADD_DOCTOR = 'event.doctor.add';
    const ON_EDIT_DOCTOR = 'event.doctor.edit';
    const ON_DELETE_DOCTOR = 'event.doctor.delete';
    
    const ON_ADD_HELPER_TEXT = 'event.helper_text.add';
    const ON_EDIT_HELPER_TEXT = 'event.helper_text.edit';
    const ON_DELETE_HELPER_TEXT = 'event.helper_text.delete';
    
    const ON_ADD_MEDICAL_PROVIDER_GROUP = 'event.medical_provider_group.add';
    const ON_EDIT_MEDICAL_PROVIDER_GROUP = 'event.medical_provider_group.edit';
    const ON_DELETE_MEDICAL_PROVIDER_GROUP = 'event.medical_provider_group.delete';
    
    
    
}