var OfferedService = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var OfferedServiceCollection = Backbone.Collection.extend({
    model: OfferedService,
    
    url: '/app_dev.php/api/offered-services',
    
    parse: function(response) {
        return response['offeredServices'];
    }
})