var InstitutionMedicalProcedure = {
		
	manageProceduresUrl : "",
	loadProcedureTypesUrl : "",
	loadProceduresUrl : "",
	updateProcedureStatusUrl : "",

	init : function(params)
	{

		this.manageProceduresUrl = params.manageProceduresUrl;
		this.loadProcedureTypesUrl = params.loadProcedureTypesUrl;
		this.loadProceduresUrl = params.loadProceduresUrl;
		this.updateProcedureStatusUrl = params.updateProcedureStatusUrl;
		
		this.initMedicalCenterOnChangeEvent();
		this.initMedicalProcedurTypeOnChangeEvent();
		
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
		})
	},

	initMedicalCenterOnChangeEvent : function()
	{
		$('#institutionMedicalProcedure_medical_center').change(function(){
			if($(this).val()) {
				$(this).prev('ul').hide();
			}
			var $procedureTypeElem = $('#institutionMedicalProcedure_procedure_type');

			$.getJSON(InstitutionMedicalProcedure.loadProcedureTypesUrl, {medical_center_id: $(this).val()}, function(types){

				if(!types.length) {
					alert('No Procedure Types yet for this center!');
					return false;
				}

				var options = InstitutionMedicalProcedure.convertToOptionsString(types);
				$procedureTypeElem.html(options);
				$('#institutionMedicalProcedure_procedure_type').change();
			});
		});	
	},
	
	initMedicalProcedurTypeOnChangeEvent : function()
	{
		$('#institutionMedicalProcedure_procedure_type').change(function(){
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
	},

	convertToOptionsString : function(data)
	{
		var len = data.length, options = '';
		for(var i=0; i< len; i++) {
			options += '<option value="'+ data[i].id +'">' + data[i].name + '</option>';
		}
		return options;
	},
}