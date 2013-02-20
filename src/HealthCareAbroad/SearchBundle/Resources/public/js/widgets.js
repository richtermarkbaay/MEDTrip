/**
 * @author Allejo Chris Velarde
 */
var HomepageSearchWidget = {
    
    treatmentsSource: null,
    
    destinationsSource: null,
    
    remoteSourceUri: '',
    
    dropdownMenuTriggerElement: '.search_dropdown_button',
    
    dataAttributeNames: {
        'list-container' : 'data-list-container',
        'autocomplete': 'data-autocomplete-field',
        'dropdown-button': 'data-dropdown-button'
    },
    
    setSourceUri: function(_v) {
        this.remoteSourceUri = _v;
        return this;
    },
        
    initializeByWidgetContainer: function(_widgets) {
        
        // load all source
        this.loadAllSources();
        
        $.each(_widgets, function(index, widgetContainer){
            // initialize dropdown
            $($(widgetContainer).attr(HomepageSearchWidget.dataAttributeNames.dropdown-button)).click(function(){
                
            });
            
        });
        
    },

    // load treatment and destinations sources
    loadAllSources: function() {
        $.ajax({
           url: HomepageSearchWidget.remoteSourceUri,
           data: {type:"all"},
           dataType: 'json',
           success: function(response) {
               HomepageSearchWidget.treatmentsSource = response.treatments;
               HomepageSearchWidget.destinationsSource = response.destination;
           }
        });
    },
    
    loadTreatmentSource: function() {
        
    },
    
    loadDestinationSource: function() {
        
    }
    
    
    
}