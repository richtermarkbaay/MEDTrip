/**
 * Handler for client-side functionalities in institution profile page
 */
var InstitutionProfile = {
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionServices':'',
        'loadInstitutionAwards': ''
    },
    
    medicalCenterTabbedContentElement: null,
    
    servicesTabbedContentElement: null,
    
    awardsTabbedContentElement: null,
    
    // jQuery DOM element for the tabbed content
    tabbedContentElement: null,
    
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    setTabbedContentElement: function(_val) {
        InstitutionProfile.tabbedContentElement = _val;
        
        return this;
    },
    
    setMedicalCentersTabbedContentElement: function(_val) {
        InstitutionProfile.medicalCenterTabbedContentElement = _val;
        
        return this;
    },
    
    
    setServicesTabbedContentElement: function(_val) {
        InstitutionProfile.servicesTabbedContentElement = _val;
        
        return this;
    },
    
    setAwardsTabbedContentElement: function(_val) {
        InstitutionProfile.awardsTabbedContentElement = _val;
        
        return this;
    },
    
    switchTab: function(_tab_element_key)
    {
        switch (_tab_element_key) {
            case 'medical_centers':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.medicalCenterTabbedContentElement.html());
                break;
            case 'services':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.servicesTabbedContentElement.html());
                break;
            case 'awards':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.awardsTabbedContentElement.html());
                break;
        }
        
        return this;
    },
    
    loadTabbedContentsOfMultipleCenterInstitution: function() {
        // medical centers content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadActiveMedicalCenters,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.medicalCenterTabbedContentElement.html(response.medicalCenters.html);
                InstitutionProfile.switchTab('medical_centers');
            }
        });
        
        // institution services content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadInstitutionServices,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.servicesTabbedContentElement.html(response.services.html);
            }
        });
        
        // awards content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadInstitutionAwards,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.awardsTabbedContentElement.html(response.awards.html);
            }
        });
        
        return this;
    }
}