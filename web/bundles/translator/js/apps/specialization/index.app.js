var SpecializationIndexApp = BaseIndexApp.extend({
    
    el: $('#specialization_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new SpecializationCollection([])
        this.indexUrl = options.indexUrl;
    },
    
    getItemView : function(specialization) {
        return new TreatmentsTranslationCollectionItemView({
            model: specialization,
            indexUrl: this.indexUrl
        });
    }
});