$(document).ready(function(){
    
    //DDSLICk
    $('#social-select-a').ddslick();
    $('#social-select-b').ddslick();

    $('#formwizard').stepy({
            backLabel:  'Previous',
            block:    true,
            errorImage: true,
            nextLabel:  'Next',
            validate: true,
            titleClick: true
    });     
    
    /* Specializations Start */
    $("#specializationsAccordion").hide();
    
    $('#specializationsTypeahead').typeahead({
        source: ['Specialization 1', 'Specialization 2'],
        updater: function(item){ 
            $("#specializationsAccordion").show();
            var selected = $("#specializationsAccordion").find(".accordion-heading:contains('"+item+"')");            
            $(selected).parent().addClass('selected');            
            toggleSelectedSpecializations();  
            return item;
        }
    }).attr('autocomplete',false);
    toggleSelectedSpecializations();
       
    
    function toggleSelectedSpecializations(){
       $("#specializationsAccordion").show()
       $("#specializationsAccordion").find('.accordion-heading').filter(function(){

            if($(this).parent().hasClass('selected')){                
                $(this).parent().show();
                if(! $(this).next().hasClass('in')){
                    $(this).find('.accordion-toggle').click();
                }
            }else{
                $(this).parent().hide();
            }
        });
    }
    
    /* Specializations End */
    
    /* Services Start */
    
    $("#servicesAccordion").hide();
    $('#servicesTypeahead').typeahead({
        source: ['Service 1', 'Service 2'],
        updater: function(item){
            $("#servicesAccordion").show();
            var selected = $("#servicesAccordion").find(".accordion-heading:contains('"+item+"')");
            
            $(selected).parent().addClass('selected');            
            toggleSelectedServices();
            /*
            if($("#specializationsList").find("tr td:contains('"+item+"')").length >0){
                alert('You have added  '+item+' already.');
            }else{
                //add selected
                $("#specializationsList").append('<tr><td>'+item+'</td></tr>');
            }*/
            return item;
        }
    }).attr('autocomplete',false);
    toggleSelectedServices();
       
    
    function toggleSelectedServices(){
       $("#servicesAccordion").show()
       $("#servicesAccordion").find('.accordion-heading').filter(function(){
            
            if($(this).parent().hasClass('selected')){                
                $(this).parent().show();
                if(! $(this).next().hasClass('in')){
                    $(this).find('.accordion-toggle').click();
                }
            }else{
                $(this).parent().hide();
            }
        });
    }
    
    /* Services End */
    
    
    /* Awards Start */
    var awards =  [
        { "name":"Award 1" , "organization":"Organization 1","year":"2012" },  
        { "name":"Award 2" , "organization":"Organization 2","year":"2012"  },  
        { "name":"Award 3" , "organization":"Organization 3","year":"2012"  }
    ];
    var editRowIndex = 0;
    var awardObj = {};//selected award
    var addAnotherAward = 'Add your own Award';
    
    $('#awardsTypeahead').typeahead({
        source: ['Award 1', 'Award 2', 'Award 3'],
        updater: function(item){
            if(item==addAnotherAward){
                $('#awardsModal').find('.award_name').val('');
                $('#awardsModal').find('.award_organization').val('');
                $('#awardsModal').find('.award_year').val('');
                $('#awardsModal').modal('toggle');
            }else{
                $.each(awards, function(i, v) {
                    if(v.name==item){
                       awardObj = v;  
                       setAwardFormData();
                    }
                });
                $('#awardsModal').find('.award_addBtn').show();
                $('#awardsModal').find('.award_updateBtn').hide();
                addAward();
            }
            //return item;
        },
        sorter: function(items){
            items.push(addAnotherAward)
            return items;
        }
    }).attr('autocomplete',false);
    
    $("#addAward").click(function(){
        $('#awardsModal').find('.award_addBtn').show();
        $('#awardsModal').find('.award_updateBtn').hide();
        awardObj.name = '';
        awardObj.organization ='';
        awardObj.year = '';
        setAwardFormData();
        $('#awardsModal').modal('toggle');
    });
    
    
    
    //Award Modal Add Btn Click
    
    //Modal Add Btn Click
    $('#awardsModal').find('.award_addBtn').click(function(){
        addAward();
        $('#awardsModal').modal('toggle');
        return false;
    });
    
    
    function addAward(){
        var $tr    = $("#awardsList").find('.awardTplRow');//template row for awards listing
        var $clone = $tr.clone();
        
        awardObj.name = $('#awardsModal').find('.award_name').val(); 
        awardObj.organization = $('#awardsModal').find('.award_organization').val();
        awardObj.year = $('#awardsModal').find('.award_year').val();
        
        $clone.find('h5').html(awardObj.name);
        $clone.find('small').html(awardObj.organization);
        $clone.find('td:eq(1)').html(awardObj.year);
        var awardYear = $('#awardsModal').find('.award_year').val();
        var yearColumnID = Math.floor(Math.random() * 0x10000).toString(8); 
        $clone.find('td:eq(1)').html(awardYear).attr('id',yearColumnID);
         
        $clone.removeClass('awardTplRow');
         
        //Award Edit Btn Click
        $clone.find('.award_editBtn').click(function(){   
            
            $('#awardsModal').find('.award_addBtn').hide();
            $('#awardsModal').find('.award_updateBtn').show();
            var $tr = $(this).parent().parent();
            
            editRowIndex = $tr[0].rowIndex;
            awardObj.name = $tr.find('h5').html();
            awardObj.organization = $tr.find('small').html();
            awardObj.year = $clone.find('td:eq(1)').html();            
            setAwardFormData();
            //$('#awardsModal').modal('toggle');
        });
         
        //Award Delete Btn Click
        $clone.find('.award_deleteBtn').click(function(){
           $(this).parent().parent().fadeOut().remove();
        });
        
        $clone.show(); 
        $tr.after($clone);
        //make year column editable
        $("#"+yearColumnID).editable({
            type: 'text',
            send:'never',
            title: 'Edit Year'
        });
        //$('#awardsModal').modal('toggle');
    }
    
    //Modal Update Btn Click
    $('#awardsModal').find('.award_updateBtn').click(function(){
        var $tr = $("#awardsList").find('tr:eq('+editRowIndex+')');
        
        awardObj.name = $('#awardsModal').find('.award_name').val(); 
        awardObj.organization = $('#awardsModal').find('.award_organization').val();
        awardObj.year = $('#awardsModal').find('.award_year').val();
        
        
        $tr.find('h5').html(awardObj.name);
        $tr.find('small').html(awardObj.organization);
        $tr.find('td:eq(1)').html(awardObj.year);
        var awardYear = $('#awardsModal').find('.award_year').val();
        $tr.find('td:eq(1)').html(awardYear);  
        $('#awardsModal').modal('toggle');
        return false;
    });
    
    function setAwardFormData(){
        $('#awardsModal').find('.award_name').val(awardObj.name); 
        $('#awardsModal').find('.award_organization').val(awardObj.organization);
        $('#awardsModal').find('.award_year').val(awardObj.year);
    }
     
    /* Awards End */
    
    
    /* Specialists Start */
    var specialists =  [
        { "firstname":"Fname1" ,"lastname":"Lname1" , "specialization":"Specialization 1"},  
        { "firstname":"Fname2" ,"lastname":"Lname2" , "specialization":"Specialization 2"},  
        { "firstname":"Fname3" ,"lastname":"Lname3" , "specialization":"Specialization 3"},  
    ];
    var editSpecialistRowIndex = 0;
    var specialistObj = {};//selected specialist
    var addAnotherSpecialist = 'Add your own Specialist';
    $('#specialistsTypeahead').typeahead({
        source: ['Lname1, Fname1','Lname2, Fname2','Lname3, Fname3'],
        updater: function(item){
            if(item==addAnotherSpecialist){
                $('#specialistsModal').find('.specialist_firstname').val('');
                $('#specialistsModal').find('.specialist_lastname').val('');
                $('#specialistsModal').find('.specialist_specialization').val('');
                $('#specialistsModal').modal('toggle');
            }else{
                $.each(specialists, function(i, v) {
                    var specialistFullname = v.lastname+', '+v.firstname;

                    if(specialistFullname==item){                    
                       specialistObj = v;  
                       setSpecialistFormData();
                    }
                });
                $('#specialistsModal').find('.specialist_addBtn').show();
                $('#specialistsModal').find('.specialist_updateBtn').hide();
                addSpecialist();
            }
            //return item;
        },
        sorter: function(items){
            items.push(addAnotherSpecialist)
            return items;
        }
    }).attr('autocomplete',false);
    
    function addSpecialist(){
        var $tr = $("#specialistsList").find('.specialistTplRow');//template row for specialists listing
        var $clone = $tr.clone();
        
        specialistObj.firstname = $('#specialistsModal').find('.specialist_firstname').val(); 
        
        specialistObj.lastname = $('#specialistsModal').find('.specialist_lastname').val();
        specialistObj.specialization = $('#specialistsModal').find('.specialist_specialization').val();
        
        $clone.find('h5 .lastname').html(specialistObj.lastname);        
        $clone.find('h5 .firstname').html(specialistObj.firstname);
        $clone.find('small .specialization').html(specialistObj.specialization);
        $clone.removeClass('specialistTplRow');
         
        //Specialist Edit Btn Click
        $clone.find('.specialist_editBtn').click(function(){   
            
            $('#specialistsModal').find('.specialist_addBtn').hide();
            $('#specialistsModal').find('.specialist_updateBtn').show();
            var $tr = $(this).parent().parent();
            
            editSpecialistRowIndex = $tr[0].rowIndex;
            specialistObj.lastname = $tr.find('h5 .lastname').html();
            specialistObj.lastname = $tr.find('h5 .firstname').html();
            specialistObj.specialization = $tr.find('small .specialization').html();
            setSpecialistFormData();
            $('#specialistsModal').modal('toggle');
        });
         
        //Specialist Delete Btn Click
        $clone.find('.specialist_deleteBtn').click(function(){
           $(this).parent().parent().fadeOut().remove();
        });
        
        $clone.show(); 
        $tr.after($clone); 
    }
    
    //Modal Add Btn Click
    $('#specialistsModal').find('.specialist_addBtn').click(function(){
        addSpecialist();
        $('#specialistsModal').modal('toggle');
        return false;
    });
    
    //Modal Update Btn Click
    $('#specialistsModal').find('.specialist_updateBtn').click(function(){
        var $tr = $("#specialistsList").find('tr:eq('+editSpecialistRowIndex+')');
        
        specialistObj.firstname=$('#specialistsModal').find('.specialist_firstname').val(); 
        specialistObj.lastname=$('#specialistsModal').find('.specialist_lastname').val(); 
        specialistObj.specialization=$('#specialistsModal').find('.specialist_specialization').val();
        
        $tr.find('h5 .lastname').html(specialistObj.lastname);        
        $tr.find('h5 .firstname').html(specialistObj.firstname);
        $tr.find('small .specialization').html(specialistObj.specialization);
        
        $('#specialistsModal').modal('toggle');
        return false;
    });
    
    function setSpecialistFormData(){
        $('#specialistsModal').find('.specialist_firstname').val(specialistObj.firstname); 
        $('#specialistsModal').find('.specialist_lastname').val(specialistObj.lastname); 
        $('#specialistsModal').find('.specialist_specialization').val(specialistObj.specialization);
    }
     
    /* Specialists End */
    
    
});


