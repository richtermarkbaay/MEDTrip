var AwardingBody = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var AwardingBodyCollection = Backbone.Collection.extend({
    model: AwardingBody,
    
    url: '/app_dev.php/api/awarding-bodies',
    
    parse: function(response) {
        return response['awardingBodies'];
    }
})