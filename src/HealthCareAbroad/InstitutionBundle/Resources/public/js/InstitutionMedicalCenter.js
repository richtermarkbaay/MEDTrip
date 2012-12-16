/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
    _modals: {
        'name': null,
        'description': null
    },
    
    _commonDialogOptions: {
        position: ['center', 100],
        autoOpen: false,
        width: 'auto',
        modal: true,
        resizable: false,
        close: function() {}
    },
    
    _callbacks: {},

    initializeModals: function(_modalOptions){
        if (_modalOptions.name) {
            this._modals.name = _modalOptions.name;
            this._modals.name.dialog(this._commonDialogOptions);
        }
        
        if (_modalOptions.description) {
            this._modals.description = _modalOptions.description;
            this._modals.description.dialog(this._commonDialogOptions);
        }
        
        return this;
    },
    
    openModal: function(_name) {
        this._modals[_name].dialog("open");
        
    },
    
    registerCallback: function(_name, _callback){
        
    },
    
    submitModalForm: function(_formElement){
        
    }
}

