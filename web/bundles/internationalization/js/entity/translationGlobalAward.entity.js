var TranslationGlobalAward = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationGlobalAwardCollection = Backbone.Collection.extend({
    model: TranslationGlobalAward,
    
    url: ApiUtility.getApiRootUrl()+'/global-award-translation',
    
    parse: function(response) {
        return response['translationGlobalAwards'];
    }
});