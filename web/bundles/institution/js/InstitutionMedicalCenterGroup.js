/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenterGroup = {
       
    specializationsForm: {
        loadInstitutionMedicalCentersUri: '',
        loadInstitutionTreatmentProceduresUri: '',
        institutionMedicalCenterField: null,
        institutionTreatmentField: null,
        institutionTreatmentProcedureField: null
    },

    initSpecializationsForm: function(params){
        InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionMedicalCentersUri = params.loadInstitutionMedicalCentersUri ? params.loadInstitutionMedicalCentersUri : '/medical-centers/load-available-specializations/';
        InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionTreatmentProceduresUri = params.loadInstitutionTreatmentProceduresUri ? params.loadInstitutionTreatmentProceduresUri : '/medical-centers/load-available-treatments/';
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField = params.institutionMedicalCenterField ? params.institutionMedicalCenterField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentField = params.institutionTreatmentField ? params.institutionTreatmentField : null;
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentProcedureField = params.institutionTreatmentProcedureField ? params.institutionTreatmentProcedureField : null;
        
        //InstitutionMedicalCenterGroup.loadAvailableInstitutionMedicalCenters();
        
        // bind change event to institution medical center dropdown
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField
            .bind("change", function(event){
                InstitutionMedicalCenterGroup.loadAvailableTreatmentsForInstitutionMedicalCenter(event.target);
            }).change();
        
    },
    
    loadAvailableInstitutionMedicalCenters: function() {
        
        InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField.html("<option value='0'>Loading...</option>").attr('disabled', true);
        
        $.ajax({
           url: InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionMedicalCentersUri,
           type: 'get',
           dataType: 'json',
           success: function(json) {
               InstitutionMedicalCenterGroup.specializationsForm.institutionMedicalCenterField
                   .html(json.html).attr('disabled', false).change();
           }
        });
        
        return false;
    },
    
    /**
     * DEPRECATED?? - Currently not being used.
     * NOTE Added by: Adelbert D. Silla
     */
    loadAvailableTreatmentsForInstitutionMedicalCenter: function(el) {
        InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentProcedureField.html("");
        _mcId = $(el).val();
        $.ajax({
           url: InstitutionMedicalCenterGroup.specializationsForm.loadInstitutionTreatmentProceduresUri,
           data: {'medicalCenterId': _mcId},
           dataType: 'json',
           type: 'get',
           success: function(json) {
               InstitutionMedicalCenterGroup.specializationsForm.institutionTreatmentProcedureField.html(json.html);
           }
        });
    }
}


