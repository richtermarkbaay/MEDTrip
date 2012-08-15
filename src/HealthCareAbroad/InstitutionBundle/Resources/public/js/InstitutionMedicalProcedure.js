var InstitutionMedicalProcedure = {
	
	selectedMedicalCenter: 0,
	selectedMedicalProcedureType: 0,
	manageProceduresUrl : "",
	loadProcedureTypesUrl : "",
	loadProceduresUrl : "",
	updateProcedureStatusUrl : "",
	medicalCenterDropdown: null,
	medicalProcedureTypeDropdown: null,

	init : function(params)
	{

		this.manageProceduresUrl = params.manageProceduresUrl;
		this.loadProcedureTypesUrl = params.loadProcedureTypesUrl;
		this.loadProceduresUrl = params.loadProceduresUrl;
		this.updateProcedureStatusUrl = params.updateProcedureStatusUrl;
		
		// init containers
		this.medicalCenterDropdown = params.medicalCenterDropdown ? params.medicalCenterDropdown : $('#institutionMedicalProcedure_medical_center');
		this.medicalProcedureTypeDropdown = params.medicalProcedureTypeDropdown ? params.medicalProcedureTypeDropdown: $('#institutionMedicalProcedure_procedure_type');
		this.selectedMedicalCenter = params.selectedMedicalCenter ? params.selectedMedicalCenter : 0;
		this.selectedMedicalProcedureType = params.selectedMedicalProcedureType ? params.selectedMedicalProcedureType : 0;
		
		if (InstitutionMedicalProcedure.selectedMedicalCenter) {
			
			InstitutionMedicalProcedure.medicalCenterDropdown.find('option').each(function(){
				if (this.value == InstitutionMedicalProcedure.selectedMedicalCenter) {
					this.selected = "selected";
				}
			});
		}
		
		this.initMedicalCenterOnChangeEvent();
		//this.initMedicalProcedurTypeOnChangeEvent();
		
		/**
		$('#procedure_filter').change(function(){
			window.location = InstitutionMedicalProcedure.manageProceduresUrl + '?filter=' + $(this).val();
		});
		
		$('#institutionMedicalProcedure_procedure_type').click(function(){
			if(!$('#institutionMedicalProcedure_medical_center').val()) {
				alert('Please choose Medical Center first.');
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
		InstitutionMedicalProcedure.medicalCenterDropdown.change(function(){
			
			medicalCenterId = $(this).val() ? $(this).val() : 0;
			
			// no medical center id, do not request ajax anymore
			if (!medicalCenterId) {
				InstitutionMedicalProcedure.medicalProcedureTypeDropdown.attr('disabled', true).html("");
				return false;
			}
			
			InstitutionMedicalProcedure.medicalProcedureTypeDropdown.attr('disabled', true).html("<option><i>Loading choices...</i></option>");
			
			$.ajax({
				url: InstitutionMedicalProcedure.loadProcedureTypesUrl,
				data: {medical_center_id: medicalCenterId},
				type: 'get',
				dataType: 'json',
				success: function(types) {
					if(!types.length) {
						InstitutionMedicalProcedure.medicalProcedureTypeDropdown.html("")
							.attr('disabled', true);
						return false;
					}

					var options = InstitutionMedicalProcedure.convertToOptionsString(types);
					InstitutionMedicalProcedure.medicalProcedureTypeDropdown.html(options)
						.attr('disabled', false)
						.change();
				},
				error: function() {
					InstitutionMedicalProcedure.medicalProcedureTypeDropdown.attr('disabled', true).html("");
				}
			});
		})
		.change();
	},
	/**
	initMedicalProcedurTypeOnChangeEvent : function()
	{
		InstitutionMedicalProcedure.medicalProcedureTypeDropdown.change(function(){
			if($(this).val()) {
				$(this).prev('ul').hide();
			}

			var $procedureElem = $('#institutionMedicalProcedure_medical_procedure');

			$.getJSON(InstitutionMedicalProcedure.loadProceduresUrl, {procedure_type_id: $(this).val()}, function(procedures){
				
				if(!procedures.length) {
					alert('No Procedures yet for this type!.');
					return false;
				}

				var options = InstitutionMedicalProcedure.convertToOptionsString(procedures);
				$procedureElem.html(options).prev('ul').hide();
			});
		});
	},**/

	convertToOptionsString : function(data)
	{
		var len = data.length, options = '';
		for(var i=0; i< len; i++) {
			options += '<option value="'+ data[i].id +'" ' + (InstitutionMedicalProcedure.selectedMedicalProcedureType==data[i].id?'selected': '') + '>' + data[i].name + '</option>';
		}
		return options;
	}
}