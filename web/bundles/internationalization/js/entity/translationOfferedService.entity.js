var TranslationOfferedService = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationOfferedServiceCollection = Backbone.Collection.extend({
    model: TranslationOfferedService,
    
    url: ApiUtility.getApiRootUrl()+'/offered-service-translation',
    
    parse: function(response) {
        return response['translationOfferedServices'];
    }
});