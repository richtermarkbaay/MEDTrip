var InstitutionSpecializationView = Backbone.View.extend({
    tagName: 'tr',
    
    className: '',
    
    render: function(){
        var imc = this.model.get('institutionMedicalCenter');
        var institution = imc.institution;
        var imcUrl = ApiUtility.getRootUrl()+'/admin/institutions/'+institution.id+'/medical-centers/'+imc.id;
        
        var institutionCol = $('<td class="col-institution"></td>').html(institution.name);
        var imcCol = $('<td class="col-imc"></td>').html(imc.name);
        
        var actionsCol = $('<td class="col-action"></td>').append(
            $('<a href="'+imcUrl+'" class="btn">View</a>')
        );
        
        this.$el
            .append(institutionCol)
            .append(imcCol)
            .append(actionsCol);
    }
});