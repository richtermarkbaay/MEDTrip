var OfferedServiceTranslationView = BaseTranslationView.extend({
    
    getFormModal: function(model, language, context){
        var modal = new OfferedServiceTranslationModalFormView({
            model: model,
            context: context,
            language: language
        });
        
        return modal;
    },
    
    getTemplateData: function(model, language, context, isEditable){
        var tplData = {
            language: language,
            nameToTranslate: context.name,
            contextTranslation: {
                id: model.get('id'),
                name: model.get('name'),
            },
            isEditable: isEditable
        };
        
        return tplData;
    }
});