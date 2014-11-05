var InstitutionTranslationView = BaseTranslationView.extend({
    
    getFormModal: function(model, language, context){
        var modal = new InstitutionTranslationModalFormView({
            model: model,
            context: context,
            language: language
        });
        
        return modal;
    },
    
    getTemplateData: function(model, language, context, isEditable){
        var name = this.model.get('name');
        var description = this.model.get('description');
        
        var tplData = {
            language: this.language,
            nameToTranslate: this.context.name,
            contextTranslation: {
                id: this.model.get('id'),
                name: name ? unescape(name) : '',
                description: description ? unescape(description) : ''
            },
            isEditable: isEditable
        };
        
        return tplData;
    }
});