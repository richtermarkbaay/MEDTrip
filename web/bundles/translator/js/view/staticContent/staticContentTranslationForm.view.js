var StaticContentTranslationModalFormView = Bootstrap3ModalForm.extend({
    
    el: $('#modal_static_content_translation_form'),
    
    fieldMapping: {
        'language': 'staticContentTranslation[language]',
        'translation': 'staticContentTranslation[translation]',
        'staticContent': 'staticContentTranslation[staticContent]'
    },
    
    populateForm: function() {
        // remove staticContent field since we don't allow changing this
        this.findField('staticContent').parents('.form-group').remove();
        
        if (this.model.isNew()) {
            this.findField('translation').val('');
            this.findField('language').val(0);
            this.$el.find('.modal-header h3').html('Add Translation for '+this.model.get('staticContent').token);
        }
        else {
            this.findField('translation').val(this.model.get('translation'));
            this.findField('language').val(this.model.get('language').id);
            this.$el.find('.modal-header h3').html('Edit Translation for '+this.model.get('staticContent').token);
        }
    },
    
    getData: function() {
        var jsonData = this.form.serializeJSON();
        jsonData['staticContentTranslation']['staticContent'] = this.model.get('staticContent').id;
        
        return jsonData;
    }
    
});