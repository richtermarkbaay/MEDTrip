var MedicalCenterIndexApp = BaseIndexApp.extend({
    
    el: $('#medical_center_index_app_canvass'),
    
    customInitialization: function(options){
        this.collection = new MedicalCenterCollection([]);
        this.viewUrl = options.viewUrl;
        
        if (options.activeInstitution > 0) {
            this.fetchCollection({'institution': this.$el.find('select[name="filters[institution]"]').val() });
        } else {
            this.fetchCollection({});
        }
    },
    
    getItemView: function(item){
        return new MedicalCenterCollectionItemView({
            model: item,
            viewUrl: this.viewUrl
        });
    },
    
    onSubmitSearchFilterForm: function(e) {
        e.preventDefault();
        var form = this.$el.find('form#form_search_filter');
        var institutionFilter = form.find('select[name="filters[institution]"]').val();
        var name = form.find('input[name="filters[name]"]').val();

        var filters = {};

        if (institutionFilter != 0) {
            filters.institution = institutionFilter;
        }
        
        if (name) {
            filters.name = name;
        }

        this.fetchCollection(filters);
    },
    
    onClickPagination: function(e) {
        e.preventDefault();
        if(!$(e.target).parent('li').hasClass('active')){
            var form = this.$el.find('form#form_search_filter');
            var filters = {
                name: form.find('input[name="filters[name]"]').val(),
                page : $(e.target).attr('page')
            };
            
            var institutionFilter = form.find('select[name="filters[institution]"]').val();
            if (institutionFilter != 0) {
                filters.institution = institutionFilter;
            }
            
            this.fetchCollection(filters);
            window.scrollTo(0,0);
        }
    }
});