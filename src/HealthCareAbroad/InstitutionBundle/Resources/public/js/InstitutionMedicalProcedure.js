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
	},

	initMedicalCenterOnChangeEvent : function()
	{
		$('#institutionMedicalProcedure_medical_center').change(function(){
			var $procedureTypeElem = $('#institutionMedicalProcedure_procedure_type');
			$procedureTypeElem.attr('disabled', true);

			$.getJSON(InstitutionMedicalProcedure.loadProcedureTypesUrl + '/' + $(this).val(), function(types){

				if(!types.length) {
					alert('No Procedure Types yet for this center!');
					return false;
				}

				var options = InstitutionMedicalProcedure.convertToOptionsString(types);
				$procedureTypeElem.html(options).attr('disabled', false);
				$('#institutionMedicalProcedure_procedure_type').change();
			});
		});	
	},
	
	initMedicalProcedurTypeOnChangeEvent : function()
	{
		$('#institutionMedicalProcedure_procedure_type').change(function(){
			var $procedureElem = $('#institutionMedicalProcedure_medical_procedure');
			$procedureElem.attr('disabled', true);

			$.getJSON(InstitutionMedicalProcedure.loadProceduresUrl + '/' + $(this).val(), function(procedures){
				var options = InstitutionMedicalProcedure.convertToOptionsString(procedures);
				$procedureElem.html(options).attr('disabled', false);
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

	updateStatus : function(elem)
	{
		id = elem.attr('id').split('-').pop();
		$.getJSON(InstitutionMedicalProcedure.updateProcedureStatusUrl, {institution_medical_procedure_id:id}, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			}
		});
	}
}