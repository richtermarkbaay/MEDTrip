var Tag = {
	baseUrl : '/app_dev.php/',

	init : function()
	{
		$('.tag-autocomplete').each(function(){
			Tag.assignAutocomplete($(this));
		});
	},

	split : function(val)
	{
		return val.split( /,\s*/ );
	},

	extractLast : function(term)
	{
		return Tag.split(term).pop();
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
				$.getJSON( Tag.baseUrl + "tags/search/" + Tag.extractLast(elem.val()) , response );
			},
			search: function() {
				// custom minLength
				var term = Tag.extractLast( this.value );
				if ( term.length < 2 ) {
					return false;
				}
			},
			focus: function() {
				// prevent value inserted on focus
				return false;
			},
			select: function( event, ui ) {
				var terms = Tag.split( this.value );
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
		$.getJSON(Tag.baseUrl + "admin/tag/update-status/" + elemId, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			}
		});
	}
}

Tag.init();