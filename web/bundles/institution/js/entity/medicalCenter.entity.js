var MedicalCenter = Backbone.Model.extend({
    isNew: function(){
        return this.get('id') == 0;
    },
    getAddressAsString: function()
    {
        var jsonAddress = this.get('address') || false;
        var address = "";
        
        if(jsonAddress){
            var parsed = $.parseJSON(jsonAddress);
            var validValues = [];
            
            $.each(parsed,function(key, val){
                if(val != "")
                    validValues[validValues.length] = val;
            });
            
            address = validValues.join(', ');
        }
        
        return address.replace(/,,/g,',').replace(/,$/,'');
    },
});

var MedicalCenterCollection = Backbone.Collection.extend({
    model: MedicalCenter,
    
    url: ApiUtility.getApiRootUrl()+'/medical-center-list',

    parse: function(response) {
        return response['medicalCenters'];
    }
});