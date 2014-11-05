var OfferedServiceViewApp = BaseViewApp.extend({
    el: $('#offered_service_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationOfferedServiceCollection([]);
    },
    
    getPrototypeModelData: function(item, contextId){
        var modelData = {
            languageIso: item.iso,
            name: null,
            context: contextId
        };
        
        return modelData;
    },
    
    getTranslationWidgetView: function(model, language, context){
        var view = new OfferedServiceTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});