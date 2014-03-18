var InstitutionInquiryView = Backbone.View.extend({
    tagName: 'tr',
    className: 'institution-inquiry-row',
    
    initialize: function(options) {
        this.listenTo(this.model, 'destroy', this.remove);
    },
    
    render: function(){
        var tplData = {
            institutionInquiry: this.model.attributes
        };
        var prototype = ich.institution_inquiry_view_prototype(tplData);
        this.$el.html(prototype.html());
    }
});