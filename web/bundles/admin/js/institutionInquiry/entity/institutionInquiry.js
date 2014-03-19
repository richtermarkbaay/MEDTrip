var InstitutionInquiry = Backbone.Model.extend({
    defaults: {
        id: 0
    },
    isNew: function(){
        return this.get('id') == 0;
    }
});

var InstitutionInquiryCollection = Backbone.Collection.extend({
    model: InstitutionInquiry,
    
    parse: function(response) {
        return response['institutionInquiries'];
    }
});