var SubSpecializationViewApp = BaseViewApp.extend({
    el: $('#sub_specialization_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationSubSpecializationCollection([]);
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
        var view = new TreatmentsCommonTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});