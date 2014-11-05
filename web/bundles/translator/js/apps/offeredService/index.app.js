var OfferedServicesIndexApp = BaseIndexApp.extend({
    
    el: $('#offered_service_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new OfferedServiceCollection([]);
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    getItemView: function(item){
        return new OfferedServiceCollectionItemView({
            model: item,
            addTranslationUrl: this.addTranslationUrl
        });
    }
});