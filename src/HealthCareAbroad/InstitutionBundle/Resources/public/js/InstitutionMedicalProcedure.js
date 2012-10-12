var InstitutionTreatmentProcedure = {
	
	selectedMedicalCenter: 0,
	selectedTreatment: 0,
	manageProceduresUrl : "",
	loadProcedureTypesUrl : "",
	loadProceduresUrl : "",
	updateProcedureStatusUrl : "",
	medicalCenterDropdown: null,
	treatmentDropdown: null,

	init : function(params)
	{

		this.manageProceduresUrl = params.manageProceduresUrl;
		this.loadProcedureTypesUrl = params.loadProcedureTypesUrl;
		this.loadProceduresUrl = params.loadProceduresUrl;
		this.updateProcedureStatusUrl = params.updateProcedureStatusUrl;
		
		// init containers
		this.medicalCenterDropdown = params.medicalCenterDropdown ? params.medicalCenterDropdown : $('select#institutionTreatmentForm_medicalCenter');
		this.treatmentDropdown = params.treatmentDropdown ? params.treatmentDropdown: $('select#institutionTreatmentForm_treatment');
		this.selectedMedicalCenter = params.selectedMedicalCenter ? params.selectedMedicalCenter : 0;
		this.selectedTreatment = params.selectedTreatment ? params.selectedTreatment : 0;
		
		if (InstitutionTreatmentProcedure.selectedMedicalCenter) {
			
			InstitutionTreatmentProcedure.medicalCenterDropdown.find('option').each(function(){
				if (this.value == InstitutionTreatmentProcedure.selectedMedicalCenter) {
					this.selected = "selected";
				}
			});
		}
		
		this.initMedicalCenterOnChangeEvent();
		//this.initMedicalProcedurTypeOnChangeEvent();
		
		/**
		$('#procedure_filter').change(function(){
			window.location = InstitutionTreatmentProcedure.manageProceduresUrl + '?filter=' + $(this).val();
		});
		
		$('#institutionMedicalProcedure_procedure_type').click(function(){
			if(!$('#institutionMedicalProcedure_medical_center').val()) {
				alert('Please choose Specialization first.');
			}
		});
		
		$('#institutionMedicalProcedure_medical_procedure').click(function(){
			if(!$('#institutionMedicalProcedure_procedure_type').val()) {
				alert('Please choose Procedure Type first.');
			}
		})**/
	},

	initMedicalCenterOnChangeEvent : function()
	{
		InstitutionTreatmentProcedure.medicalCenterDropdown.change(function(){
			
			medicalCenterId = $(this).val() ? $(this).val() : 0;
			
			// no specialization id, do not request ajax anymore
			if (!medicalCenterId) {
				InstitutionTreatmentProcedure.treatmentDropdown.attr('disabled', true).html("");
				return false;
			}
			
			InstitutionTreatmentProcedure.treatmentDropdown.attr('disabled', true).html("<option><i>Loading choices...</i></option>");
			
			$.ajax({
				url: InstitutionTreatmentProcedure.loadProcedureTypesUrl,
				data: {medical_center_id: medicalCenterId},
				type: 'get',
				dataType: 'json',
				success: function(types) {
					if(!types.length) {
						InstitutionTreatmentProcedure.treatmentDropdown.html("")
							.attr('disabled', true);
						return false;
					}

					var options = InstitutionTreatmentProcedure.convertToOptionsString(types);
					InstitutionTreatmentProcedure.treatmentDropdown.html(options)
						.attr('disabled', false)
						.change();
				},
				error: function() {
					InstitutionTreatmentProcedure.treatmentDropdown.attr('disabled', true).html("");
				}
			});
		})
		.change();
	},
	/**
	initMedicalProcedurTypeOnChangeEvent : function()
	{
		InstitutionTreatmentProcedure.treatmentDropdown.change(function(){
			if($(this).val()) {
				$(this).prev('ul').hide();
			}

			var $procedureElem = $('#institutionMedicalProcedure_medical_procedure');

			$.getJSON(InstitutionTreatmentProcedure.loadProceduresUrl, {procedure_type_id: $(this).val()}, function(procedures){
				
				if(!procedures.length) {
					alert('No Procedures yet for this type!.');
					return false;
				}

				var options = InstitutionTreatmentProcedure.convertToOptionsString(procedures);
				$procedureElem.html(options).prev('ul').hide();
			});
		});
	},**/

	convertToOptionsString : function(data)
	{
		var len = data.length, options = '';
		for(var i=0; i< len; i++) {
			options += '<option value="'+ data[i].id +'" ' + (InstitutionTreatmentProcedure.selectedTreatment==data[i].id?'selected': '') + '>' + data[i].name + '</option>';
		}
		return options;
	}
}