var TreatmentView = Backbone.View.extend({
    tagName: 'tr',
    
    className: '',
    
    render: function(){
        var nameCol = $('<td class="col-name"></td>').html(this.model.get('name'));
        
        var subs = [];
        _.each(this.model.get('subSpecializations'), function(item, key, list){
            subs.push(item.name);
        });
        var subSpecializationsCol = $('<td class="col-sub-specializations"></td>').html(subs.join(', '));
        var descriptionCol = $('<td class="col-description"></td>').html(this.model.get('description'));
        var statusCol = $('<td class="col-status"></td>').html(this.model.getStatusLabel());
        var actionsCol = $('<td class="col-actions"></td>');
        
        this.$el
            .append(nameCol)
            .append(subSpecializationsCol)
            .append(descriptionCol)
            .append(statusCol)
            .append(actionsCol);
        
    }
});