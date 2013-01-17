$(document).ready(function(){
    var specializationObj = {
        title:"",
        treatments:""        
    };//specialization Object
    
    /*addAnotherSpecialization*/ 
    $("#addAnotherSpecialization").click(function(e){
        e.preventDefault();
        specializationObj = {
            title:"",
            treatments:""        
        };
        setSpecializationForm();
        $('#specializationModal').find('.specialization_addBtn').show();
        $('#specializationModal').find('.specialization_updateBtn').hide();
        $("#specializationModal").modal('toggle');
    });
    
    $("#specializationModal").find('.specialization_addBtn').click(function(){                
        addSpecialization();
        $("#specializationModal").modal('toggle');
    });
    var editRowIndex = 0;
    //Modal Update Btn Click
    $('#specializationModal').find('.specialization_updateBtn').click(function(){
        specializationObj.title =  $("#specializationModal").find('.specialization_title').val();
        specializationObj.treatments = $("#specializationModal").find('.specialization_treatments').val();
        var $tr = $("#specializationList").find('tr:eq('+editRowIndex+')');
        $tr.find('.title').html(specializationObj.title);
        $tr.find('.treatments').html(specializationObj.treatments);
        $('#specializationModal').modal('toggle');
        return false;
    });
    
    function addSpecialization(){
        specializationObj.title =  $("#specializationModal").find('.specialization_title').val();
        specializationObj.treatments = $("#specializationModal").find('.specialization_treatments').val();
        
        var $tr    = $("#specializationList").find('.specializationTplRow');//template row for specialization listing
        
        var $clone = $tr.clone();
        $clone.find('.title').html(specializationObj.title);
        $clone.find('.treatments').html(specializationObj.treatments);         
        $clone.removeClass('specializationTplRow');
         
        //Award Edit Btn Click
        $clone.find('.specialization_editBtn').click(function(){            
            $('#specializationModal').find('.specialization_addBtn').hide();
            $('#specializationModal').find('.specialization_updateBtn').show();
            var $tr = $(this).closest('tr');
            
            editRowIndex = $tr[0].rowIndex;
            specializationObj.title = $tr.find('.title').html();
            specializationObj.treatments = $tr.find('.treatments').html();
            setSpecializationForm();
            $('#specializationModal').modal('toggle');
        });
         
        //Award Delete Btn Click
        $clone.find('.specialization_deleteBtn').click(function(){
           $(this).closest('tr').fadeOut().remove();
        });        
        $clone.show(); 
        $tr.after($clone);        
        //$('#awardsModal').modal('toggle');
    }
    
    function setSpecializationForm(){
        $('#specializationModal').find('.specialization_title').val(specializationObj.title); 
        $('#specializationModal').find('.specialization_treatments').val(specializationObj.treatments);
    }
    
 });