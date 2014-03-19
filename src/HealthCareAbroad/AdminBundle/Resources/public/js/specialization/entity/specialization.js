var Specialization = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var SpecializationCollection = Backbone.Collection.extend({
    model: Specialization,
    
    url: '/api/specializations'
})