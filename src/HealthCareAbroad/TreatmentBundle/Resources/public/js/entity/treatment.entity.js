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