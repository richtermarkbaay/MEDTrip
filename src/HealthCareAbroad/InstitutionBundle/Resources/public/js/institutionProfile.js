/**
 * Handler for client-side functionalities in institution profile page
 */
var InstitutionProfile = {
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionServices':'',
        'loadInstitutionAwards': ''
    },
    // jQuery DOM element for the tabbed content
    tabbedContentElement: null,
    
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    setTabbedContentElement: function(_val) {
        this.tabbedContentElement = _val;
        
        return this;
    },
    
    loadTabbedContents: function() {
        
    }
}