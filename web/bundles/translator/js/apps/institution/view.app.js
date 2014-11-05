var InstitutionViewApp = BaseViewApp.extend({
    el: $('#institution_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationInstitutionCollection([]);
    },
    
    getPrototypeModelData: function(item, contextId){
        var modelData = {
            languageIso: item.iso,
            name: null,
            description: null,
            context: contextId
        };
        
        return modelData;
    },
    
    getTranslationWidgetView: function(model, language, context){
        var view = new InstitutionTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});