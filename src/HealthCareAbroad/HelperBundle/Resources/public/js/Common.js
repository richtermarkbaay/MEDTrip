var HCA = {

	init : function(params)
	{
		HCA.autocompleteSearchUrl = params.autocompleteSearchUrl;
	},
	
	filterResult: function(url) {
		var params = '';
		$('#filter-wrapper .filter-params').each(function(){
			params += "&" + $(this).attr('name') +"="+ $(this).val();
		});
		
		window.location = url + '?' + params.substr(1); 
	}
};

HCA.autocomplete = {

	init : function()
	{
		$('.autocomplete-medical-center, .autocomplete-procedure-type, .autocomplete-procedure').each(function(){
			HCA.autocomplete.assignAutocomplete($(this));
		});
	},

	split : function(val)
	{
		return val.split( /,\s*/ );
	},

	extractLast : function(term)
	{
		return HCA.autocomplete.split(term).pop();
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
				var params = {
					section: elem.attr('class').split(' ').shift().replace('autocomplete-',''),
					term: HCA.autocomplete.extractLast(elem.val())
				}
				$.getJSON(HCA.autocompleteSearchUrl, params, response);
			},
			search: function() {
				// custom minLength
				var term = HCA.autocomplete.extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = HCA.autocomplete.split( this.value );
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
	}
}

$(function(){

	// Initialize HCA autocomplete object.
	HCA.autocomplete.init();
	

	// activate/deactivate status of current record 
	$('a.update-status').click(function(){
		var elem = $(this);
		var url = elem.attr('href');
		elem.attr('href', 'javascript:void(0)');

		$.getJSON(url, function(result){
			if(result) {
				elem.hasClass('icon-2') 
					? elem.removeClass('icon-2').addClass('icon-5').attr('title', 'Activate')
					: elem.removeClass('icon-5').addClass('icon-2').attr('title', 'Delete');

			} else {
				alert('Unable to activate or deactivate.');
			}
			elem.attr('href', url);
		});
	});	


});
