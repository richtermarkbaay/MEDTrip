var GlobalAward = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var GlobalAwardCollection = Backbone.Collection.extend({
    model: GlobalAward,
    
    url: '/app_dev.php/api/global-awards',
    
    parse: function(response) {
        return response['globalAwards'];
    }
})