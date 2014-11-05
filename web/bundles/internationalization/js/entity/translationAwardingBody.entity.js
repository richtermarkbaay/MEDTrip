var TranslationAwardingBody = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationAwardingBodyCollection = Backbone.Collection.extend({
    model: TranslationAwardingBody,
    
    url: ApiUtility.getApiRootUrl()+'/awarding-body-translation',
    
    parse: function(response) {
        return response['translationAwardingBodies'];
    }
});