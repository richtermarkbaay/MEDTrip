/**
 * @author Chaztine Blance
 * Auto Complete for Institution Language
 */
var LanguageAuto = {

	availableTags : 0,
	selectedTags : 0,
	
	init : function(params)
	{
		this.availableTags = params.availableTags;
		this.selectedTags = params.selectedTags;
		this.inputHiddenField = params.inputHiddenField;	
		this.inputAutoLanguage = params.inputAutoLanguage;
		
		$('.click').click(LanguageAuto.clickRemove);
		
		$(LanguageAuto.inputAutoLanguage).each(function(){
			LanguageAuto.assignAutocomplete($(this));
		});
	},
	
	split : function(val)
	{
		return val.split( /,\s*/ );
	},

	extractLast : function(term)
	{
		return LanguageAuto.split( term ).pop();			
	},
	
	log : function( message , id) 
	{
//		$( "<a/ href='#' id='language"+id+"' class='click btn btn-mini' onclick=''>" ).bind('click btn btn-mini', LanguageAuto.clickRemove).text( message ).prependTo( "#tags" );
		$("<a href='#' id='language"+id+"' class='click btn btn-mini' onclick=''><i class='icon-trash'></i> "+message+" </a> ").bind('click btn btn-mini', LanguageAuto.clickRemove).text( message ).prependTo( "#tags" );
		$("<i class='icon-trash'></i>").prependTo( "#language"+id+"" );
		$( "#tags" ).scrollTop( 0 );
	},	
	
	hidden : function ( message ) 
	{
	
		$( message ).append( LanguageAuto.inputHiddenField );
	},
	
	mergeTags : function ()
	{	
	
		var currentTerms = $(LanguageAuto.inputHiddenField).val().split(',');
	    $.each(currentTerms, function(c, cval){
	        currentTerms[c] = $.trim(cval);
	    });
	    var temp = [];
	    
	    var removedSelectedTags = [];
	    $.each(LanguageAuto.selectedTags, function(i, val){
		    temp.push(val);
	    });

	    tempLength = temp.length;
				    
	    for (i=0; i<tempLength; i++) {
	        
	        if ($.inArray(temp[i].value, currentTerms) < 0) {
	            
	            $.each(LanguageAuto.selectedTags, function(x, val){
		            if (val && val.value == temp[i].value) {
		            	LanguageAuto.selectedTags.splice(x,1);
		                removedSelectedTags.push(temp[i]);
		                return;
		            }
	            });
	        }
	    }
	    $.merge(LanguageAuto.availableTags, removedSelectedTags);
	},
	
	clickRemove : function (event)
	{
		LanguageAuto.remove(this.id);
		LanguageAuto.mergeTags();

	    return false;
	},
	
	remove : function (id)
	{
		var val = $('#'+id).text();
		var currentTerms = $(LanguageAuto.inputHiddenField).val().split(',');

	    $.each(currentTerms, function (_k, _v){
			currentTerms[_k] = $.trim(_v);    
	    });
	 	var b = $.inArray($.trim(val), currentTerms);

		if (b < 0 ) {
			console.log("false no match");
		}
		else {
			currentTerms.splice(b, 1);
		}
    	$(LanguageAuto.inputHiddenField).val($.trim(currentTerms.join(',')))
 
	    	$("#"+id).remove();
   		return true;	
	},
	
	assignAutocomplete : function(elem)
	{
		elem.bind("keyup", function(event){
		
			if ( $(this).data('autocomplete').term){
				LanguageAuto.mergeTags();
			}
		})
		.bind( "keydown", function( event ) {

			if ( event.keyCode === $.ui.keyCode.TAB &&						
	
					$( this ).data( "autocomplete" ).menu.active ) {
				event.preventDefault();					
			}			
		})
		.autocomplete({
			minLength: 1,
				create: function(event, ui) {
		            currentTerms = $(LanguageAuto.inputHiddenField).val().split(',');
		            $.each(currentTerms, function(c, cval){
				        currentTerms[c] = $.trim(cval);
				    });
		            if (currentTerms.length > 0) {
		                $.each(currentTerms, function(xi, val){
		                    $.each(LanguageAuto.availableTags, function(ii, ival){
			                    if (ival && ival.value==val) {
			                    	LanguageAuto.availableTags.splice(ii, 1);
			                    	LanguageAuto.selectedTags.push(ival);
			                        return;
			                    }
		                });		
		            });
		        }
		    },
		    source: function(request, response) {
		    	
					response( $.ui.autocomplete.filter(
							LanguageAuto.availableTags,LanguageAuto.extractLast( request.term )));	
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				var terms = LanguageAuto.split( this.value );
				terms.pop();
				// add the selected item					
				terms.push( ui.item.value );
				LanguageAuto.log( ui.item.value , ui.item.id );
	
				this.value = terms.join( "" );					
				add_field = $(LanguageAuto.inputHiddenField);
	
				 if (!add_field.val()){
					 add_field.val( add_field.val() + this.value);
					 }else{
					 add_field.val( add_field.val()  + "," + this.value );
				 }
				
				 elem.val('');

				$.each(LanguageAuto.availableTags, function(i, val){
				    if (val && val.id == ui.item.id) {
				    	LanguageAuto.selectedTags.push({'value': val.value, 'id': val.id});
					    LanguageAuto.availableTags.splice(i, 1);
				        return;
				    }
				});		
				return false;
			}
		});
	}
};

