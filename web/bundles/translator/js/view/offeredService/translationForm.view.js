var OfferedServiceTranslationModalFormView = Bootstrap3ModalForm.extend({
    
    el: $('#modal_translation_form'),
    
    fieldMapping: {
        'name': 'offeredServiceTranslation[name]',
        'languageIso': 'offeredServiceTranslation[languageIso]'
    },

    customInitialization: function(options) {
        this.language = options.language;
        this.context = options.context;
    },
    
    populateForm: function() {
        this.findField('name').val(this.model.get('name'));
        this.findField('languageIso').val(this.language.iso);

        this.$el.find('.modal-header h3').html(this.language.name + ' Translation for '+this.context.name);
    },
    
    getData: function() {
        var jsonData = this.form.serializeJSON();
        jsonData['context'] = this.model.get('context');

        return jsonData;
    }
    
});