/**
 * @author Alnie Jacobe
 * Auto Complete for Doctors
 */
var DoctorAuto = {

	availableTags : 0,
	selectedTags : 0,
	
	init : function(params)
	{
		this.availableTags = params.availableTags;
		this.selectedTags = params.selectedTags;
		this.inputHiddenField = params.inputHiddenField;	
		this.inputAutoDoctor = params.inputAutoDoctor;
		
		$('.click').click(DoctorAuto.clickRemove);
		
		$(DoctorAuto.inputAutoDoctor).each(function(){
			DoctorAuto.assignAutocomplete($(this));
		});
	},
	
	split : function(val)
	{
		return val.split( /,\s*/ );
	},

	extractLast : function(term)
	{
		return DoctorAuto.split( term ).pop();			
	},
	
	log : function( message , id) 
	{
		console.log(message);
		console.log(id);
//		$( "<a/ href='#' id='Doctor"+id+"' class='click btn btn-mini' onclick=''>" ).bind('click btn btn-mini', DoctorAuto.clickRemove).text( message ).prependTo( "#tags" );
		$("<a href='#' id='doctor"+id+"' class='click btn btn-mini' onclick=''><i class='icon-trash'></i> "+message+" </a> ").bind('click btn btn-mini', DoctorAuto.clickRemove).text( message ).prependTo( "#tags" );
		$("<i class='icon-trash'></i>").prependTo( "#doctor"+id+"" );
		$( "#tags" ).scrollTop( 0 );
	},	
	
	hidden : function ( message ) 
	{
	
		$( message ).append( DoctorAuto.inputHiddenField );
	},
	
	mergeTags : function ()
	{	
	
		var currentTerms = $(DoctorAuto.inputHiddenField).val().split(',');
	    $.each(currentTerms, function(c, cval){
	        currentTerms[c] = $.trim(cval);
	    });
	    var temp = [];
	    
	    var removedSelectedTags = [];
	    $.each(DoctorAuto.selectedTags, function(i, val){
		    temp.push(val);
	    });

	    tempLength = temp.length;
				    
	    for (i=0; i<tempLength; i++) {
	        
	        if ($.inArray(temp[i].value, currentTerms) < 0) {
	            
	            $.each(DoctorAuto.selectedTags, function(x, val){
		            if (val && val.value == temp[i].value) {
		            	DoctorAuto.selectedTags.splice(x,1);
		                removedSelectedTags.push(temp[i]);
		                return;
		            }
	            });
	        }
	    }
	    $.merge(DoctorAuto.availableTags, removedSelectedTags);
	},
	
	clickRemove : function (event)
	{
		DoctorAuto.remove(this.id);
		DoctorAuto.mergeTags();

	    return false;
	},
	
	remove : function (id)
	{
		var val = $('#'+id).text();
		var currentTerms = $(DoctorAuto.inputHiddenField).val().split(',');

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
    	$(DoctorAuto.inputHiddenField).val($.trim(currentTerms.join(',')))
 
	    	$("#"+id).remove();
   		return true;	
	},
	
	assignAutocomplete : function(elem)
	{
		elem.bind("keyup", function(event){
		
			if ( $(this).data('autocomplete').term){
				DoctorAuto.mergeTags();
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
		            currentTerms = $(DoctorAuto.inputHiddenField).val().split(',');
		            $.each(currentTerms, function(c, cval){
				        currentTerms[c] = $.trim(cval);
				    });
		            if (currentTerms.length > 0) {
		                $.each(currentTerms, function(xi, val){
		                    $.each(DoctorAuto.availableTags, function(ii, ival){
			                    if (ival && ival.value==val) {
			                    	DoctorAuto.availableTags.splice(ii, 1);
			                    	DoctorAuto.selectedTags.push(ival);
			                        return;
			                    }
		                });		
		            });
		        }
		    },
		    source: function(request, response) {
		    	
					response( $.ui.autocomplete.filter(
							DoctorAuto.availableTags,DoctorAuto.extractLast( request.term )));	
			},
			focus: function() {
				return false;
			},
			select: function( event, ui ) {
				var terms = DoctorAuto.split( this.value );
				console.log(terms);
				terms.pop();
				// add the selected item					
				terms.push( ui.item.value );
				DoctorAuto.log( ui.item.value , ui.item.id );
	
				this.value = terms.join( "" );					
				add_field = $(DoctorAuto.inputHiddenField);
	
				 if (!add_field.val()){
					 add_field.val( add_field.val() + this.value);
					 }else{
					 add_field.val( add_field.val()  + "," + this.value );
				 }
				
				 elem.val('');

				$.each(DoctorAuto.availableTags, function(i, val){
				    if (val && val.id == ui.item.id) {
				    	DoctorAuto.selectedTags.push({'value': val.value, 'id': val.id});
					    DoctorAuto.availableTags.splice(i, 1);
				        return;
				    }
				});		
				return false;
			}
		});
	}
};

