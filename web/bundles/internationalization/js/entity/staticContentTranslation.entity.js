var StaticContentTranslation = Backbone.Model.extend({
    
    defaults: {
        id: 0
    },
    
    url: function() {
        var colUrl = ApiUtility.getApiRootUrl()+'/static-content-translations';
        return this.isNew() ? colUrl : colUrl+'/'+this.get('id');
    },
    
    isNew: function(){
        return this.get('id') == 0;
    },
    
    parse: function(response, options){
        if (response['staticContentTranslation']) {
            this.set(response['staticContentTranslation']);
        }
        else {
            this.set(response);
        }
    }
});


var StaticContentTranslationCollection = Backbone.Collection.extend({
    model: StaticContentTranslation,
    
    url: function(){
        return ApiUtility.getApiRootUrl()+'/static-content-translations';
    },
    
    parse: function(response){
        return response['staticContentTranslations'];
    }
});