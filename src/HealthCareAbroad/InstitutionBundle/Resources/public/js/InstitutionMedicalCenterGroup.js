/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenterGroup = {
       
    specializationsForm: {
        loadInstitutionMedicalCentersUri: '',
        institutionMedicalCenterField: null,
        institutionTreatmentField: null,
        institutionTreatmentProcedureField: null
    },

    initSpecializationsForm: function(params){
        InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionMedicalCentersUri = params.loadInstitutionMedicalCentersUri ? params.loadInstitutionMedicalCentersUri : '/medical-centers/load-available-specializations/';
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField = params.institutionMedicalCenterField ? params.institutionMedicalCenterField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentField = params.institutionTreatmentField ? params.institutionTreatmentField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentProcedureField = params.institutionTreatmentProcedureField ? params.institutionTreatmentProcedureField : null;
        
        // bind change event to institution medical center dropdown
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField
            .bind("change", InstitutionMedicalCenterGroup.loadAvailableInstitutionMedicalCenters)
            .change();
        
    },
    
    loadAvailableInstitutionMedicalCenters: function() {
        
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField.html("<option value='0'>Loading...</option>").attr('disabled', true);
        
        $.ajax({
           url: InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionMedicalCentersUri,
           type: 'get',
           dataType: 'json',
           success: function(json) {
               InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField.html(json.html).attr('disabled', false);
           }
        });
        
        return false;
    }
}


