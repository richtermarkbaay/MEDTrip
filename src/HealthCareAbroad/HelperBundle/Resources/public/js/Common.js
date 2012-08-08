$(function(){

	
	// activate/deactivate status of current record 
	$('a.update-status').click(function(){
		var elem = $(this);
		var url = elem.attr('href');
		elem.attr('href', 'javascript:void(0)');

		$.getJSON(url, function(result){
			if(result) {
				var status = $.trim(elem.html()) == 'activate';
				elem.html(status ? 'deactivate' : 'activate');				
			} else {
				alert('Unable to activate or deactivate.');
			}
			elem.attr('href', url);
		});
	});	

});
