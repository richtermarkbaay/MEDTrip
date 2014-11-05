var AwardingBodyIndexApp = BaseIndexApp.extend({
    
    el: $('#awarding_body_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new AwardingBodyCollection([]);
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    getItemView: function(item){
        return new AwardingBodyCollectionItemView({
            model: item,
            addTranslationUrl: this.addTranslationUrl
        });
    }
});