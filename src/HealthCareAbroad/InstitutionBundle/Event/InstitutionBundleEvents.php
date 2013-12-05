<?php
namespace HealthCareAbroad\InstitutionBundle\Event;

final class InstitutionBundleEvents
{
    const ON_ADD_INSTITUTION = 'event.institution.add';
    const ON_EDIT_INSTITUTION = 'event.institution.edit';
    const ON_DELETE_INSTITUTION = 'event.institution.delete';
    const ON_UPDATE_STATUS_INSTITUTION = 'event.institution.update_status';
    
    //const ON_ADD_INSTITUTION_INVITATION = 'event.institution_invitation.add';
    
    const ON_ADD_INSTITUTION_MEDICAL_CENTER = 'event.institution_medical_center.add';
    const ON_EDIT_INSTITUTION_MEDICAL_CENTER = 'event.institution_medical_center.edit';
    const ON_DELETE_INSTITUTION_MEDICAL_CENTER = 'event.institution_medical_center.delete';
    const ON_UPDATE_STATUS_INSTITUTION_MEDICAL_CENTER = 'event.institution_medical_center.update_status';
    
    const ON_ADD_INSTITUTION_TREATMENT_PROCEDURE = 'event.institution_medical_procedure.add';
    const ON_EDIT_INSTITUTION_TREATMENT_PROCEDURE = 'event.institution_medical_procedure.edit';
    const ON_DELETE_INSTITUTION_TREATMENT_PROCEDURE = 'event.institution_medical_procedure.delete';
    
    const ON_ADD_INSTITUTION_TREATMENT = 'event.institution_medical_procedure_type.add';
    const ON_EDIT_INSTITUTION_TREATMENT = 'event.institution_medical_procedure_type.edit';
    const ON_DELETE_INSTITUTION_TREATMENT = 'event.institution_medical_procedure_type.delete';
    
    const ON_ADD_INSTITUTION_USER = 'event.institution_user.add';
    const ON_EDIT_INSTITUTION_USER = 'event.institution_user.edit';
    const ON_DELETE_INSTITUTION_USER = 'event.institution_user.delete';
    const ON_LOGIN_INSTITUTION_USER = 'event.institution_user.login';
    const ON_CHANGE_PASSWORD_INSTITUTION_USER  = 'event.institution_user.change_password';
    
    const ON_ADD_INSTITUTION_USER_INVITATION = 'event.institution_user_invitation.add';
    
    const ON_ADD_INSTITUTION_USER_TYPE_ROLE= 'event.institution_user_type.add_role';
    const ON_DELETE_INSTITUTION_USER_TYPE_ROLE = 'event.institution_user_type.delete_role';
    
    const ON_ADD_INSTITUTION_USER_TYPE= 'event.institution_user_type.add';
    const ON_EDIT_INSTITUTION_USER_TYPE = 'event.institution_user_type.edit';
    const ON_DELETE_INSTITUTION_USER_TYPE = 'event.institution_user_type.delete';
    
    const ON_ADD_INSTITUTION_FEEDBACK= 'event.institution_feedback.add';
    const ON_EDIT_INSTITUTION_FEEDBACK = 'event.institution_feedback.edit';
    const ON_DELETE_INSTITUTION_FEEDBACK = 'event.institution_feedback.delete';
    
    const ON_ADD_FEEDBACK_MESSAGE= 'event.feedback_message.add';
}