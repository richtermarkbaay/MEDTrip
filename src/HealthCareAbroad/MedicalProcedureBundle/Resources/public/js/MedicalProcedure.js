var MedicalProcedure = {
	baseUrl : '/app_dev.php/',
	
	updateStatus : function(elem)
	{
		elemId = elem.attr('id').split('-').pop();
		$.getJSON(Tag.baseUrl + "admin/medical-procedure/update-status/" + elemId, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			}
		});
	}
}

Tag.init();