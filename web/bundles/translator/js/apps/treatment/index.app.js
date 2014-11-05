var TreatmentIndexApp = BaseIndexApp.extend({
    
    el: $('#treatment_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new TreatmentCollection([])
        this.indexUrl = options.indexUrl;
    },
    
    getItemView : function(specialization) {
        return new TreatmentsTranslationCollectionItemView({
            model: specialization,
            indexUrl: this.indexUrl
        });
    }
});