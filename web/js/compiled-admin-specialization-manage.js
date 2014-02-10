var CommonConfirmModal= Backbone.View.extend({
    tagName: 'div',
    
    className: 'modal hide fade modal-box',
    
    events: {
        'hidden': 'onHideModal'
    },
    
    initialize: function(){
        this.$el
            .attr('role', 'dialog')
            .attr('tabindex', "-1");
        
        this.rendered = false;
    },
    
    onHideModal: function(e) {
        this.remove();
    },
    
    render: function(){
        var prototype = ich.common_confirm_modal_prototype();
        this.$el.html(prototype.html());
        
        this.$el.appendTo($('body'));
        
        this.$el.find('.modal-header h3').html(this.header || ''); // set the header
        this.$el.find('.modal-body').html(this.body || ''); // set the body
        this.$el.find('.modal-footer').html(this.footer || '');
        
        this.rendered = true;
    },
    
    hide: function() {
        this.$el.modal('hide');
    },
    
    show: function() {
        if (!this.rendered) {
            this.render();
        }
        this.$el.modal('show');
    }
    
});
/**
 * keep this file simple and match only the API
 */
var Treatment = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    },
    
    getStatusLabel: function() {
        return this.get('status') == 1
            ? 'Active'
            : 'Inactive';
    }
});

var TreatmentCollection = Backbone.Collection.extend({
    model: Treatment, 
    url: ApiUtility.getApiRootUrl()+'/treatments',
    parse: function(response) {
        return response['treatments'];
    }
});
var SubSpecialization = Backbone.Model.extend({
    isNew: function(){
        return this.get('id') == 0;
    }
});

var SubSpecializationCollection = Backbone.Collection.extend({
    model: SubSpecialization, 
    url: ApiUtility.getApiRootUrl()+'/sub-specializations',
    parse: function(response) {
        return response['subSpecializations'];
    }
});
var InstitutionSpecialization = Backbone.Model.extend({
    isNew: function(){
        return this.get('id') == 0;
    }
});

var InstitutionSpecializationCollection = Backbone.Collection.extend({
    model: InstitutionSpecialization,
    
    url: ApiUtility.getApiRootUrl()+'/institution-specializations',
    
    parse: function(response) {
        return response['institutionSpecializations'];
    }
});


var TreatmentView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'treatment-row',
    
    render: function(){
        var treatmentUrl = ApiUtility.getRootUrl()+'/admin/treatments/'+this.model.get('id')+'/edit'
        var nameCol = $('<td class="col-name"></td>').html(this.model.get('name'));
        
        var subSpecializationsCol = $('<td class="col-sub-specializations"></td>')
            .append($('<ul style="list-style: none outside none; margin: 0px;"></ul>'));
        
        _.each(this.model.get('subSpecializations'), function(item, key, list){
            var url = ApiUtility.getRootUrl()+'/admin/sub-specializations/'+item.id+'/edit';
            var li = $('<li></li>').append($('<a href="'+url+'"></a>').html(item.name));
            subSpecializationsCol.find('ul').append(li);
        });
        
        var descriptionCol = $('<td class="col-description"></td>').html(this.model.get('description'));
        var statusCol = $('<td class="col-status"></td>').html(this.model.getStatusLabel());
        var actionsCol = $('<td class="col-actions"></td>');
        actionsCol.append($('<a href="'+treatmentUrl+'" class="btn"></a>').html('View'));
        
        this.$el
            .append(nameCol)
            .append(subSpecializationsCol)
            .append(descriptionCol)
            .append(statusCol)
            .append(actionsCol);
        
    }
});
var InstitutionSpecializationView = Backbone.View.extend({
    tagName: 'tr',
    
    className: '',
    
    render: function(){
        var imc = this.model.get('institutionMedicalCenter');
        var institution = imc.institution;
        var imcUrl = ApiUtility.getRootUrl()+'/admin/institutions/'+institution.id+'/medical-centers/'+imc.id;
        
        var institutionCol = $('<td class="col-institution"></td>').html(institution.name);
        var imcCol = $('<td class="col-imc"></td>').html(imc.name);
        
        var actionsCol = $('<td class="col-action"></td>').append(
            $('<a href="'+imcUrl+'" class="btn">View</a>')
        );
        
        this.$el
            .append(institutionCol)
            .append(imcCol)
            .append(actionsCol);
    }
});
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