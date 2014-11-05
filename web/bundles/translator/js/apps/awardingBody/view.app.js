var AwardingBodyViewApp = BaseViewApp.extend({
    el: $('#awarding_body_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationAwardingBodyCollection([]);
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
        var view = new AwardingBodyTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});