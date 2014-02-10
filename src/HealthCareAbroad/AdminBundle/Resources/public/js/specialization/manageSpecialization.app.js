var ManageSpecializationApp = Backbone.View.extend({
    el: $('#app_canvass'),
    
    initialize: function(options){
        this.specialization = options.specialization || null;
        
        // init treatments
        this.treatmentCollection = new TreatmentCollection([]);
        this.treatmentCollectionContainer = this.$el.find('.treatment-collection');
        this.listenTo(this.treatmentCollection, 'add', this.onAddTreatment);
        
        var treatmentLoader = this.treatmentCollectionContainer.find('.loader');
        this.treatmentCollection.fetch({
            data: {specialization: this.specialization.id},
            success: function(response){
                treatmentLoader.hide();
            }
        });
        
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
    },
    
    onAddTreatment: function(treatment){
        var view = new TreatmentView({
            model: treatment
        });
        
        view.render();
        this.treatmentCollectionContainer.append(view.$el);
    },
    
    onAddInstitutionSpecialization: function(institutionSpecialization) {
        var view  = new InstitutionSpecializationView({model: institutionSpecialization});
        view.render();
        
        this.institutionSpecializationCollectionContainer.append(view.$el);
    }
});