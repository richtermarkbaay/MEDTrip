var TranslationMedicalCenter = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationMedicalCenterCollection = Backbone.Collection.extend({
    model: TranslationMedicalCenter,
    
    url: ApiUtility.getApiRootUrl()+'/medical-center-translation',
    
    parse: function(response) {
        return response['translationMedicalCenters'];
    }
});