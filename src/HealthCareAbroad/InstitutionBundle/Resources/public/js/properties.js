/**
 * @author allejochrisvelarde
 */

(function($){
    function split( val ) {
        return val.split( /,\s*/ );
    }
    
    function extractLast( term ) {
        return split( term ).pop();
    }
    
    function _showModal(_modal) {
        _modal.modal('show');
    }
    
    function _hideModal(_modal) {
        _modal.modal('hide');
    }
    
    /***
     * handler for Global Award related js functionalites
     */
    $.globalAward = {
        _editForm: null
    };
    
    $.globalAward.options = {
        'year_acquired_json_key': 'year_acquired',
        'edit': {
            'modal': null, // jQuery element for the modal container
            'data_label_target': '', // identifier of the element that will be replaced by value of data-label attr
            'input_award_id': 'input.globalAwardId', // identifier of the hidden input element that will hold the value of the award id 
            'input_extraValueAutocomplete_json': 'input.extraValueAutocomplete_json', // identifier of the hidden input element that will hold the JSON value of the extraValue field
            'input_extraValueAutocomplete': 'input.extraValueAutocomplete', // identifier of the input text element that will hold the  value of the extraValue
            'submit_button': 'button.submit', // identifier of the submit button
            'year_acquired_column': '.yearAcquired'
        },
        'autocompleteYear': {
            'minimumYear': 1920
        }
    };
    
    $.globalAward.actions = {
        'edit': function (_self) {
            
            $.globalAward._editForm = $.globalAward.options.edit.modal.find('form'); 
            
            // bind the form event
            $.globalAward._editForm.submit($.globalAward._submitEditForm);
            
            // bind click event
            $(_self).click($.globalAward._clickEdit);
            
            return _self;
        },
        'autocompleteYear': function (_self) {
            
            _year = new Date().getFullYear();
            availableTags = [];
            while (_year >= $.globalAward.options.autocompleteYear.minimumYear) {
                availableTags.push(_year.toString());
                _year--;
            }
            // autocomplete jquery plugin
            _self.bind('keydown', function (event){
                if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ){
                    event.preventDefault();
                }
            }).autocomplete({
                minLength: 2,
                source: function( request, response ) {
                    // delegate back to autocomplete, but extract the last term
                    response($.ui.autocomplete.filter(availableTags, extractLast( request.term ) ) );
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function( event, ui ) {
                    var terms = split( this.value );
                    // remove the current input
                    terms.pop();
                    // value has not been pushed yet
                    if (terms.indexOf(ui.item.value)< 0) {
                        // add the selected item
                        terms.push( ui.item.value );
                        // add placeholder to get the comma-and-space at the end
                        terms.push( "" );
                    }

                    this.value = terms.join( ", " ).replace(/,\s*$/,''); // trime last ,
                    return false;
                }
            });
            
            return _self;
        }
    };
    
    $.globalAward._clickEdit = function(_event) {
        _el = $(this);
        $.globalAward._editForm.attr('action', _el.attr('href'));
        
        // find the globalAwardId element and replace the value with the data-globalAwardId attr
        $.globalAward._editForm.find($.globalAward.options.edit.input_award_id)
            .val(_el.attr('data-globalAwardId'));
        
        $.globalAward.options.edit.data_label_target.html(_el.attr('data-label')); // replace data label value
        //$.globalAward.options.edit.modal.find($.globalAward.options.edit.input_extraValueAutocomplete).val(_el.attr('data-propertyExtraValue'))// initialize autocomplete field values
        _showModal($.globalAward.options.edit.modal);
        
        return false;
    };
    // submit edit form handler
    $.globalAward._submitEditForm = function(_event) {
        _form = $(this);
        _button = _form.find($.globalAward.options.edit.submit_button);
        _buttonHtml = _button.html();
        _button.html(_button.attr('data-loading-text')).attr('disabled', true);
        _autocomplete = _form.find($.globalAward.options.edit.input_extraValueAutocomplete);
        // convert autocomplete value to JSON
        
        var _b = {
            'year_acquired': _autocomplete ? split(_autocomplete.val()) : []
        };
        // NOTE: JSON is only available in modern browsers, IE8, FF, Chrome
        _extraValueJSON = window.JSON.stringify(_b);
        // update value of hidden extraValue field
        _form.find($.globalAward.options.edit.input_extraValueAutocomplete_json).val(_extraValueJSON);
        $.ajax({
            url: _form.attr('action'),
            type: 'post',
            data: _form.serialize(),
            dataType: 'json',
            success: function(response) {
                _currentRow = $(response.targetRow);
                // currently only replace year acquired
                _currentRow.find($.globalAward.options.edit.year_acquired_column).html(response.html);
                _button.html(_buttonHtml).attr('disabled', false);
                _hideModal($.globalAward.options.edit.modal);
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        
        return false;
    };
    
    $.fn.globalAward = function(_action, _options){
        
        $.extend($.globalAward.options[_action] || {}, _options);
        
        return $.globalAward.actions[_action]
            ? $.globalAward.actions[_action](this)
            : this;
    };
    
    /** ==============================
     * end of global award related JS functionality
     */
    
})(jQuery);
