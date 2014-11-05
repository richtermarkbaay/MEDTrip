var Institution = Backbone.Model.extend({
    defaults: {
        id: 0
    },
    
    isNew: function(){
        return this.get('id') == 0;
    },
    
//    parse: function(response, options) {
//        if (response['institution']) {
//            this.set(response['institution']);
//        }
//        else {
//            this.set(response);
//        }
//    },
//    
//    // convenience function to render complete address as string
    completeAddressAsString: function() {
        var completeAddress = this.completeAddressAsArray();
        var validValues = [];
        
        $.each(completeAddress, function(key, val){
            if(val != "" && val != null && val != undefined)
                validValues[validValues.length] = val.trim();
        }); 
        
        var stringAddress = validValues.join(', ');
        return stringAddress.replace(/,,/g,',').replace(/,$/,'');
    },
    
    completeAddressAsArray: function() {
        var completeAddress = [];
        
        var keys = ['room_number','building','street'];
        var address1 = this.get('address1');
        try{
            address1 = $.parseJSON(address1);
        }catch(e){}
        completeAddress[0] = this.addressPartAsString(address1, keys);
        
        keys = ['name'];
        completeAddress[1] = this.addressPartAsString(this.get('city'), keys);
        completeAddress[2] = this.addressPartAsString(this.get('state'), keys);
        completeAddress[3] = this.addressPartAsString(this.get('country'), keys);
        completeAddress[4] = this.get('zipCode');
        
        return completeAddress;
    },
    
    addressPartAsString: function(addressPart, includedKeys, glue) {
        var address = "";
        var validValues = [];
        
        if(addressPart){
            $.each(includedKeys,function(key, val){
                var value = addressPart[val];
                if(value != "" )
                    validValues[validValues.length] = value;
            }); 
        }
        
        if(typeof(glue)==='undefined')
            glue = ', ';
        
        address = validValues.join(glue);
        
        return address;
    },
});

var InstitutionCollection = Backbone.Collection.extend({
    model: Institution,
    
    url: function(){
        return ApiUtility.getApiRootUrl()+'/institutions';
    },
    
    parse: function(response) {
        return response['institutions'];
    }
});

