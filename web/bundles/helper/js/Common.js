var HCA = {
	alertContainerElem: null,
	tinymceConfig: null,
	date: null,
	current: null,
		
	init : function(params)
	{
		//HCA.autocompleteSearchUrl = params.autocompleteSearchUrl;
		HCA.alertContainerElem = $('#confirmation-message');

		HCA.tinymceConfig = params.tinymceConfig;
	},
	
	filterResult: function(url) {
		var params = '';
		$('#filter-wrapper .filter-params').each(function(){
			params += "&" + $(this).attr('name') +"="+ $(this).val();
		});
		window.location = url + '?' + params.substr(1); 
	},

	getTimestamp: function(strDate) {
		var date = new Date();
		var datum = Date.parse(strDate);
		return datum/1000;
	},

	alertMessage: function(type, message)
	{
		HCA.alertContainerElem.find('div.confirmation-box').attr('class', 'confirmation-box fixed');
		HCA.alertContainerElem.find('.confirmation-box').html('<div class="alert alert-'+type+'"><p>' + message + '</p><a id="_close-confirmation-message" class="close" href="javascript:HCA.closeAlertMessage()"><i class="icon-remove"></i></a></div>');
		HCA.alertContainerElem.fadeIn('fast');
	},
	
	closeAlertMessage: function()
	{
		HCA.alertContainerElem.fadeOut();
	},
	
	displayInlineErrorMessage: function(elem, message)
	{
		errorTemplate = '<ul class="error"><li>'+message+'</li></ul>';
		elem.addClass('error').append(errorTemplate);
	},
	
	removeInlineErrorMessage: function(elem)
	{
		elem.removeClass('error').find('ul').remove();
	}
};

/**
 * @NOTE Do not Remove! This is being used in tinyMCE initialization at shared_parameters.yml
 */
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

function ucwords (str) {
	return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
		return $1.toUpperCase();
	});
}

$(function(){

	// Confirmation Close button 
    $('#_close-confirmation-message').live('click', function(e) {
        $("#confirmation-message").fadeOut();
    });


	// activate/deactivate status of current record 
	$('a.update-status').click(function(){
		var elem = $(this);
		var url = elem.attr('href');
		elem.attr('href', 'javascript:void(0)');

		$.post(url, function(result){
			if(result) {
				var spanElem = elem.find('span');
				if(spanElem.html() == 'Activate') {
					elem.attr('title', 'Deactivate');
					elem.find('i').addClass('icon-remove').removeClass('icon-ok');
				} else {
					elem.attr('title', 'Activate');
					elem.find('i').addClass('icon-ok').removeClass('icon-remove');
				}
				elem.find('span').html(elem.attr('title'));
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

//	$('.dropdown-toggle').click(function(){ // temporary disabled
//		$('ul.dropdown-menu').hide();
//		$(this).next('ul.dropdown-menu').toggle();
//	});
	
	
	$('#main-content').click(function(){
		$('#main-nav .dropdown-menu, #right-nav .dropdown-menu').hide();		
	});
});