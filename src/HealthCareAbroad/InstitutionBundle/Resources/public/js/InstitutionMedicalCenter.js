/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
    
    procedureDialog: null,

    /**
     * set the procedure dialog box
     * @params DOMElement dialogContainer
     * @params options - see jquery ui dialog options
     */
    setProcedureDialog: function(dialogContainer, options) {
        this.procedureDialog = dialogContainer;
        this.procedureDialog.dialog(options);
    },
    
    /**
     * handler for add procedure link actions
     * @params jquery element linkElement the element that dispatched the event
     */
    addProcedure: function(linkElement) {
        if (this.procedureDialog) {
            this.procedureDialog.dialog('option','title', 'Add Medical Procedure');
            this._showProcedureForm(linkElement);
        }
        return false;
    },
    
    editProcedure: function(linkElement) {
        if (this.procedureDialog) {
            this.procedureDialog.dialog('option','title', 'Edit Medical Procedure');
            this._showProcedureForm(linkElement);
        }
        return false;
    },
    
    _showProcedureForm: function(linkElement) {
        _url = linkElement.attr('href');
        this.procedureDialog.dialog({
            open: function() {
                $.ajax(_url)
                    .done(function (data) {
                        InstitutionMedicalCenter.procedureDialog.html(data);
                    }
                );
            },
        });
        this.procedureDialog.dialog("open");
    }
}