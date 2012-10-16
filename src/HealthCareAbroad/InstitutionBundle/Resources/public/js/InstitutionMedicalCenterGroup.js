/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenterGroup = {
       
    specializationsForm: {
        institutionMedicalCenterField: null,
        institutionTreatmentField: null,
        institutionTreatmentProcedureField: null
    },

    initSpecializationsForm: function(params){
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField = params.institutionMedicalCenterField ? params.institutionMedicalCenterField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentField = params.institutionTreatmentField ? params.institutionTreatmentField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentProcedureField = params.institutionTreatmentProcedureField ? params.institutionTreatmentProcedureField : null;
    }
}