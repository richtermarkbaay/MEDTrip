var AwardingBodyTranslationModalFormView = Bootstrap3ModalForm.extend({
    
    el: $('#modal_translation_form'),
    
    fieldMapping: {
        'name': 'awardingBodyTranslation[name]',
        'details': 'awardingBodyTranslation[details]',
        'languageIso': 'awardingBodyTranslation[languageIso]'
    },

    customInitialization: function(options) {
        this.language = options.language;
        this.context = options.context;
    },
    
    populateForm: function() {
        this.findField('name').val(this.model.get('name'));
        this.findField('details').val(this.model.get('details'));
        this.findField('languageIso').val(this.language.iso);

        this.$el.find('.modal-header h3').html(this.language.name + ' Translation for '+this.context.name);
    },
    
    getData: function() {
        var jsonData = this.form.serializeJSON();
        jsonData['context'] = this.model.get('context');

        return jsonData;
    }
    
});