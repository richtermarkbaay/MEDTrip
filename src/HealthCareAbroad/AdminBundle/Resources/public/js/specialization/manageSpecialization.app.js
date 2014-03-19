var ManageSpecializationApp = Backbone.View.extend({
    el: $('#app_canvass'),
    
    events: {
        'click button.submit-filter': 'onSubmitTreatmentFilter'
    },
    
    initialize: function(options){
        this.specialization = options.specialization || null;
        
        // init treatments
        this.treatmentCollection = new TreatmentCollection([]);
        this.treatmentCollectionContainer = this.$el.find('.treatment-collection');
        this.listenTo(this.treatmentCollection, 'add', this.onAddTreatment);
        this.listenTo(this.treatmentCollection, 'reset', this.onResetTreatmentCollection);
        this.fetchTreatmentCollection({specialization: this.specialization.id});
        
        // init linked institution specializations
        this.institutionSpecializationCollection = new InstitutionSpecializationCollection([]);
        this.institutionSpecializationCollectionContainer = this.$el.find('.institution-specialization-collection');
        this.listenTo(this.institutionSpecializationCollection, 'add', this.onAddInstitutionSpecialization);
        var institutionSpecializationLoader = this.institutionSpecializationCollectionContainer.find('.loader');
        this.institutionSpecializationCollection.fetch({
            data: {specialization: this.specialization.id},
            success: function(response) {
                institutionSpecializationLoader.hide();
            }
        });
        
        // init treatment filters
        var subSpecializationFilter = this.$el.find('.treatment-filters select[name="sub-specialization"]'); 
        this.subSpecializationCollection = new SubSpecializationCollection([]);
        this.subSpecializationCollection.fetch({
            data: {specialization: this.specialization.id},
            success: function(collection){
                subSpecializationFilter.html('').append($('<option value="all"></option>').text('Select Sub-specialization'));
                collection.each(function(model){
                    var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
                    subSpecializationFilter.append(opt);
                });
            }
        });
        
    },
    
    onAddTreatment: function(treatment){
        var view = new TreatmentView({
            model: treatment
        });
        
        view.render();
        this.treatmentCollectionContainer.append(view.$el);
    },
    
    onResetTreatmentCollection: function() {
        this.treatmentCollection.each(this.onAddTreatment, this);
    },
    
    onAddInstitutionSpecialization: function(institutionSpecialization) {
        var view  = new InstitutionSpecializationView({model: institutionSpecialization});
        view.render();
        
        this.institutionSpecializationCollectionContainer.append(view.$el);
    },
    
    onSubmitTreatmentFilter: function(e){
        e.preventDefault();
        var subSpecializationFilter = parseInt(this.$el.find('.treatment-filters select[name="sub-specialization"]').val());
        var data = {specialization: this.specialization.id};
        if (subSpecializationFilter) {
            data.subSpecialization = subSpecializationFilter;
        }
        
        this.fetchTreatmentCollection(data);
    },
    
    fetchTreatmentCollection: function(data){
        var btn = this.$el.find('.treatment-filters button.submit-filter');
        var loader = this.treatmentCollectionContainer.find('.loader');
        loader.show();
        this.treatmentCollectionContainer.find('tr.treatment-row').remove();
        btn.prop('disabled', true);
        this.treatmentCollection.fetch({
            data: data,
            success: function(response){
                loader.hide();
                btn.prop('disabled', false);
            },
            error: function(xhr, response, options) {
                loader.hide();
                btn.prop('disabled', false);
            },
            reset: true
        });
    }
});