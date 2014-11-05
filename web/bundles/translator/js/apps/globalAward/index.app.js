var GlobalAwardIndexApp = BaseIndexApp.extend({
    
    el: $('#global_award_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new GlobalAwardCollection([]);
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    getItemView: function(item){
        return new GlobalAwardCollectionItemView({
            model: item,
            addTranslationUrl: this.addTranslationUrl
        });
    }
});