var AwardingBodyTranslationView = BaseTranslationView.extend({
    
    getFormModal: function(model, language, context){
        var modal = new AwardingBodyTranslationModalFormView({
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
                details: model.get('details'),
            },
            isEditable: isEditable
        };
        
        return tplData;
    }
});