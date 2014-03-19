var TreatmentView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'treatment-row',
    
    render: function(){
        var treatmentUrl = ApiUtility.getRootUrl()+'/admin/treatments/'+this.model.get('id')+'/edit'
        var nameCol = $('<td class="col-name"></td>').html(this.model.get('name'));
        
        var subSpecializationsCol = $('<td class="col-sub-specializations"></td>')
            .append($('<ul style="list-style: none outside none; margin: 0px;"></ul>'));
        
        _.each(this.model.get('subSpecializations'), function(item, key, list){
            var url = ApiUtility.getRootUrl()+'/admin/sub-specializations/'+item.id+'/edit';
            var li = $('<li></li>').append($('<a href="'+url+'"></a>').html(item.name));
            subSpecializationsCol.find('ul').append(li);
        });
        
        var descriptionCol = $('<td class="col-description"></td>').html(this.model.get('description'));
        var statusCol = $('<td class="col-status"></td>').html(this.model.getStatusLabel());
        var actionsCol = $('<td class="col-actions"></td>');
        actionsCol.append($('<a href="'+treatmentUrl+'" class="btn"></a>').html('View'));
        
        this.$el
            .append(nameCol)
            .append(subSpecializationsCol)
            .append(descriptionCol)
            .append(statusCol)
            .append(actionsCol);
        
    }
});