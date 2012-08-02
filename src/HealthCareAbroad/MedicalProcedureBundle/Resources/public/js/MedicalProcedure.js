var MedicalProcedure = {
	baseUrl : '/app_dev.php/',
	
	updateStatus : function(elem)
	{
		elemId = elem.attr('id').split('-').pop();
		$.getJSON(MedicalProcedure.baseUrl + "admin/medical-procedure/update-status/" + elemId, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			}
		});
	}
}

MedicalProcedure.init();