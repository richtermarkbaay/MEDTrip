var SubSpecializationIndexApp = BaseIndexApp.extend({
    
    el: $('#sub_specialization_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new SubSpecializationCollection([])
        this.indexUrl = options.indexUrl;
    },
    
    getItemView : function(specialization) {
        return new TreatmentsTranslationCollectionItemView({
            model: specialization,
            indexUrl: this.indexUrl
        });
    }
});