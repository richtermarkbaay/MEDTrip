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
	deleteRow: function(elem,id)
	{
		elem.parents('tr').remove();
		var val = id;
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
	
	log : function( data ) 
	{																								 
		$('#medicalSpecialistTable tr:last').after(data);
		$( "#medicalSpecialistTable" ).scrollTop( 0 );
	},	
	
	hidden : function ( message ) 
	{
		$( message ).append( DoctorAuto.inputHiddenField );
	},
	
	assignAutocomplete : function(elem)
	{
		elem.autocomplete({
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
				var terms = DoctorAuto.split( ui.item.id );
				terms.pop();
				// add the selected item					
				terms.push( ui.item.id );
				var link = ui.item.path;
				var specifications = '';
				$( "#loader" )
	            .html('Processing...');
				$.ajax({
					  type: "POST",
					  dataType: 'JSON',
					  url: link,
					  success: function(data){
						  DoctorAuto.log(data);
						  
					   }
					 });
				$( "#loader" )
	            .html('');
				
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

