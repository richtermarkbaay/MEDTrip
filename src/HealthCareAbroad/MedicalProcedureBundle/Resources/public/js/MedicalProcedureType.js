var MedicalProcedureType = {
	baseUrl : '/app_dev.php/',

	init : function()
	{
		$('.center-autocomplete').each(function(){
			MedicalProcedureType.assignAutocomplete($(this));
		});
	},

	split : function(val)
	{
		return val.split( /,\s*/ );
	},

	extractLast : function(term)
	{
		return MedicalProcedureType.split(term).pop();
	},

	assignAutocomplete : function(elem)
	{
		// don't navigate away from the field on tab when selecting an item
		elem.bind( "keydown", function( event ) {
			if ( event.keyCode === $.ui.keyCode.TAB &&
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();
			}
		})
		.autocomplete({
			source: function( request, response ) {
				$.getJSON( MedicalProcedureType.baseUrl + "admin/medical-centers/search/" + MedicalProcedureType.extractLast(elem.val()) , response );
			},
			search: function() {
				// custom minLength
				var term = MedicalProcedureType.extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = MedicalProcedureType.split( this.value );
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push( ui.item.value );
				// add placeholder to get the comma-and-space at the end
				terms.push( "" );
				this.value = terms.join( ", " );
				return false;
			}
		});
	},
	
	updateStatus : function(elem)
	{
		elemId = elem.attr('id').split('-').pop();
		$.getJSON(MedicalProcedureType.baseUrl + "admin/procedure-type/update-status/" + elemId, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			}
		});
	}
}

MedicalProcedureType.init();