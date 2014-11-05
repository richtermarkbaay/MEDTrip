var InstitutionIndexApp = BaseIndexApp.extend({
    
    el: $('#institution_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new InstitutionCollection([]);
        this.viewAllProfileUrl = options.viewAllProfileUrl;
    },
    
    getItemView: function(item){
        return new InstitutionCollectionItemView({
            model: item,
            viewAllProfileUrl: this.viewAllProfileUrl
        });
    }
});