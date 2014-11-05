var MedicalCenterTranslationView = BaseTranslationView.extend({
    
    getFormModal: function(model, language, context){
        var modal = new MedicalCenterTranslationModalFormView({
            model: model,
            context: context,
            language: language
        });
        
        return modal;
    },
    
    getTemplateData: function(model, language, context, isEditable){
        var name = this.model.get('name');
        var description = this.model.get('description');
        var highlight = this.model.get('descriptionHighlight');
        
        var tplData = {
            language: this.language,
            medicalCenterName: this.context.name,
            medicalCenterTranslation: {
                id: this.model.get('id'),
                name: name ? unescape(name) : '',
                description: description ?  unescape(description) : '',
                highlight: highlight ?  unescape(highlight)  : '',
            },
            isEditable: isEditable
        };
        
        return tplData;
    }
});