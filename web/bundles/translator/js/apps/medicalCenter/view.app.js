var MedicalCenterViewApp = BaseViewApp.extend({
    el: $('#medical_center_view_app_canvass'),
    
    customInitialization: function(options)
    {
        this.translationCollection = new TranslationMedicalCenterCollection([]);
    },
    
    getPrototypeModelData: function(item, contextId){
        var modelData = {
            languageIso: item.iso,
            name: null,
            description: null,
            descriptionHighlight: null,
            medicalCenter: contextId
        };
        
        return modelData;
    },
    
    getTranslationWidgetView: function(model, language, context){
        var view = new MedicalCenterTranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});