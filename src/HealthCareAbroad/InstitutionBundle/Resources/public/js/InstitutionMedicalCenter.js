/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
    
    commonDialog: null,

    /**
     * set the procedure dialog box
     * @params jQueryElement dialogContainer
     * @params options - see jquery ui dialog options
     */
    setCommonDialog: function(dialogContainer, options) {
        this.commonDialog = dialogContainer;
        this.commonDialog.dialog(options);
    },
    
    /**
     * Handler for click add procedure type buttons/links 
     */
    addProcedureType: function(linkElement) {
        this.commonDialog.dialog('option','title', 'Add Medical Procedure Type');
        this._showCommonDialog(linkElement);
        return false;
    },
    
    /**
     * Handler for click edit procedure type button/links
     */
    editProcedureType: function(linkElement) {
        this.commonDialog.dialog('option','title', 'Edit Medical Procedure Type');
        this._showCommonDialog(linkElement);
        return false;
    },
    
    /**
     * handler for add procedure link actions
     * @params jquery element linkElement the element that dispatched the event
     */
    addProcedure: function(linkElement) {
        if (this.commonDialog) {
            this.commonDialog.dialog('option','title', 'Add Medical Procedure');
            this._showCommonDialog(linkElement);
        }
        return false;
    },
    
    /**
     * Handler for edit procedure link actions
     */
    editProcedure: function(linkElement) {
        if (this.commonDialog) {
            this.commonDialog.dialog('option','title', 'Edit Medical Procedure');
            this._showCommonDialog(linkElement);
        }
        return false;
    },
    
    /**
     * Show the dialog with contents retrieve from AJAX call to linkElement's href attribute
     * 
     * @internal
     */
    _showCommonDialog: function(linkElement) {
        _url = linkElement.attr('href');
        this.commonDialog.dialog({
            open: function() {
                $.ajax(_url)
                    .done(function (data) {
                        InstitutionMedicalCenter.commonDialog.html(data);
                    }
                );
            },
        });
        this.commonDialog.dialog("open");
    }
}