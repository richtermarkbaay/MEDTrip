var EntityVersionHistory = {
        
    commonDialog: null,
    
    /**
     * set the procedure dialog box
     * @params jQueryElement dialogContainer
     * @params options - see jquery ui dialog options
     */
    setCommonDialog: function(dialogContainer, options) {
        _commonOptions = {
            autoOpen: false,
            height: 'auto',
            width: 750,
            modal: true,
            resizable: false,
            close: function() {
                $('#dialog-container').html("");
            }
        };
        options = $.merge(_commonOptions, options);
        this.commonDialog = dialogContainer;
        this.commonDialog.dialog(options);
    },
    
    viewHistory: function(linkElement) {
        if (this.commonDialog) {
            this.commonDialog.dialog('option','title', 'Version History');
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
                        EntityVersionHistory.commonDialog.html(data);
                    }
                );
            },
        });
        this.commonDialog.dialog("open");
    }
        
}