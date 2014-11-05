
/**
 * StaticContent model that matches requests from API
 */
var StaticContent = Backbone.Model.extend({
    defaults: {
        id: 0
    },
    
    isNew: function() {
        return this.get('id') == 0;
    }
});

/**
 * 
 */
var StaticContentCollection = Backbone.Collection.extend({
    
    model: StaticContent,
    
    parse: function(response) {
        return response['staticContents'];
    }
});