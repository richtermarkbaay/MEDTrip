var GlobalAwardViewApp = BaseViewApp.extend({
    el: $('#awarding_body_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationGlobalAwardCollection([]);
    },
    
    getPrototypeModelData: function(item, contextId){
        var modelData = {
            languageIso: item.iso,
            name: null,
            details: null,
            context: contextId
        };
        
        return modelData;
    },
    
    getTranslationWidgetView: function(model, language, context){
        var view = new GlobalAwardTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});