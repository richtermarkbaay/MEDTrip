var HCA = {

	tinymceConfig: null,
		
	init : function(params)
	{
		HCA.autocompleteSearchUrl = params.autocompleteSearchUrl;
		
		HCA.tinymceConfig = params.tinymceConfig;
	},
	
	filterResult: function(url) {
		var params = '';
		$('#filter-wrapper .filter-params').each(function(){
			params += "&" + $(this).attr('name') +"="+ $(this).val();
		});
		
		window.location = url + '?' + params.substr(1); 
	}
};


function tinymceSetup(ed, e)
{
	// Tweak HTML5 Client Validation and apply it in tinymce editor.
	var form = $('#' + ed.id).closest('form');
	if(!form.is('[novalidate]')) {

		ed.onKeyUp.add(function(ed, e) {
			if('' != ed.getContent({format : 'text'})) {
				$('#' + ed.id + '_parent').removeClass('required');				
			}
	    });

		ed.onSubmit.add(function(ed, e) {
			if('' == ed.getContent({format : 'text'})) {
				$('#' + ed.id + '_parent').addClass('required');
				e.preventDefault();
			}
	    });

		ed.onChange.add(function(ed,e){
			ed.save();
		});

		ed.onUndo.add(function(ed,e){
			ed.save();

			if('' != ed.getContent({format : 'text'}))  {
				$('#' + ed.id + '_parent').removeClass('required');
			}
		});

		form.find('input[type=submit]').click(function(){
			var $textareas = $(this).parent().find('textarea.' + HCA.tinymceConfig.textarea_class);

			$textareas.each(function(){
				if($(this).val() == '') {
					$(this).next('span.mceEditor').addClass('required');
					return false;
				}
			});
		});
	}
}


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

		$.post(url, function(result){
			if(result) {
				elem.hasClass('icon-2') 
					? elem.removeClass('icon-2').addClass('icon-5').attr('title', 'Activate')
					: elem.removeClass('icon-5').addClass('icon-2').attr('title', 'Delete');

			} else {
				alert('Unable to activate or deactivate.');
			}
			elem.attr('href', url);
		}, "json");
	});	

	$(".sortable-list th a").click(function(){
		var arrUrl = $(this).attr('href').split('?');
		var url = arrUrl[0];
		var sortBy = $(this).parent().attr('id').split('-').pop();
		var sortOrder = $(this).hasClass('sort-asc') ? 'desc' : 'asc';
		var queryParam = {};
		
		if(arrUrl.length > 1) {
			var arrParams = arrUrl[1].split('&');
			var keyValue;

			for(var i=0; i< arrParams.length; i++) {
				keyValue = arrParams[i].split('=');
				if(keyValue[1] != 'all') {
					queryParam[keyValue[0]] = keyValue[1];					
				}
			}
		}
		queryParam.sortBy = sortBy;
		queryParam.sortOrder = sortOrder;

		url += '?' + $.param(queryParam);

		$(this).attr('href', url);
	});
	
	// remove Alert 
	$('a.remove-alert').click(function(){
		var elem = $(this);
		var url = elem.attr('href');
		elem.attr('href', 'javascript:void(0)');

		$.post(url, function(result){
			if(result.ok == true) {
				elem.parent().fadeOut();
			} else {
				alert('Unable to remove alert');
				elem.attr('href', url);
			}
		}, "json");
	});	
	
	$('.dropdown-toggle').click(function(){
		$('ul.dropdown-menu').hide();
		$(this).next('ul.dropdown-menu').toggle();
	});
});