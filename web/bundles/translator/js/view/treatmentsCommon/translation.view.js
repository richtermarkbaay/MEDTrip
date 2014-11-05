var TreatmentsCommonTranslationView = BaseTranslationView.extend({
    
    getFormModal: function(model, language, context){
        var modal = new TreatmentsCommonTranslationModalFormView({
            model: model,
            context: context,
            language: language
        });
        
        return modal;
    },
    
    getTemplateData: function(model, language, context, isEditable){
        var name = model.get('name');
        var description = model.get('description');
        
        var tplData = {
            language: language,
            nameToTranslate: context.name,
            contextTranslation: {
                id: model.get('id'),
                name: name ? unescape(name) : '',
                description: description ? unescape(description) : ''
            },
            isEditable: isEditable
        };
        
        return tplData;
    }
});