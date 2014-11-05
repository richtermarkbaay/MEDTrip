var BaseViewApp = Backbone.View.extend({

    initialize: function(options){
        this.translationContext = options.context;
        this.offeredLanguages = options.offeredLanguages;
        
        this.customInitialization(options);
        
        this.listenTo(this.translationCollection, 'add', this.onAddTranslation);
        this.listenTo(this.translationCollection, 'reset', this.onResetTranslationCollection);
        
        this.translationCollection.fetch({
            data: {context: this.translationContext.id },
            reset: true
        });
    },
    
    onResetTranslationCollection: function(){
        var contextId = this.translationContext.id;
        this.translationCollection.each(this.onAddTranslation, this);

        // iterate through all languages and create a dummy translation for those that don't have
        _.each(this.offeredLanguages, function(item, key, list){
            var hasTranslation = item.hasTranslation || false;

            if (!hasTranslation) {
                var modelData = this.getPrototypeModelData(item, contextId);
                
                if ('en' == item.iso) {
                    // we don't want to save english translation
                    this.translationCollection.add(modelData);
                } else {
                    var collection = this.translationCollection;
                    var createdTranslation = this.translationCollection.create(modelData, {success: function(response) {
                        collection.add(createdTranslation);
                    }});
                }
            }
            
        }, this);
    },
    
    onAddTranslation: function(translation){
        var languageForIso = {};
        var currentIso = translation.get('languageIso');
        
        // get the language data for this ISO code
        _.each(this.offeredLanguages, function(item, key, list){
            if (item.iso == currentIso){
                languageForIso = item;
                return false;     
            }
        });
        
        if ('en' == currentIso) {
            var englishAttribute = this.translationContext;
            englishAttribute.id = 0;
            
            translation.set(englishAttribute);
        }
        
        var view = this.getTranslationWidgetView(translation, languageForIso, this.translationContext);
        
        view.render();
        $('#translations_container').append(view.$el);
    },
    
    // override for extending view init
    customInitialization: function(options){
        
    },
    
    // override to include necessary values
    getPrototypeModelData: function(item,contextId){
        var modelData = {
            languageIso: item.iso,
            context: contextId
        };
        
        return modelData;
    },
    
    // override for custom widget view
    getTranslationWidgetView: function(model, language, context){
        var view = new TranslationView({
            model: model,
            language: language,
            context: context
        });
        
        return view;
    }
});