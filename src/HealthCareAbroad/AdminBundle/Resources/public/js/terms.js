
(function($){
    
    function split( val ) {
        return val.split( /,\s*/ );
    }
    
    function showModal(_modal) {
        _modal.modal('show');
    }
    
    function hideModal(_modal) {
        _modal.modal('hide');
    }
    
    var Terms = {
        'options': {
            'autocomplete': {
                'minLength': 1,
                'type': 0,
                'remoteUrl': '',
                'selectedContainer': '.autocompleteSelectedContainer',
                'selectedInputName': 'selectedTerms', // this is the name for the input field that will be added to the form
                'form': 'form'
            },
            'modalAddTerm': {
                'form': 'form.addTermForm',
                'submit_button': 'button.submit'
            },
            'loadCurrentTerms': {
                'url': '',
                'removeCurrentTermUrl': ''
            }
        },
        // handler for autocomplete action
        _autocomplete: function(_self) {
            
            var _currentTerms = [];
            var _xhr;
            
            _self.bind('keydown', function (event){
                if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ){
                    event.preventDefault();
                }
            }).autocomplete({
                minLength: Terms.options.autocomplete.source,
                source: function (request, response) {
                    if (_xhr && 4 != _xhr.readyState) {
                        _xhr.abort();
                    }
                    _xhr = $.ajax({
                        url: Terms.options.autocomplete.remoteUrl,
                        data: { term: _self.val(), type:Terms.options.autocomplete.type, 'selectedTems': _currentTerms },
                        type: 'get',
                        dataType: 'json',
                        success: function(json) {
                            response($.each(json, function(item) {
                                return { label: item.label, value: item.value, html: item.html }
                            }));
                        }
                    });
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function( event, ui ) {
                    
                    if (_currentTerms.indexOf(ui.item.value)< 0) {
                        // add the selected item
                        _currentTerms.push( ui.item.value );
                        
                        // add the displayed selected item
                        _newSelected = $(ui.item.html);
                        _newSelected.bind('click', function(){
                            _index = _currentTerms.indexOf($(this).html());
                            if (_index >= 0 ){
                                _currentTerms.splice(_index, 1); // remove from _currentTerms
                                
                                // remove hidden input
                                $(Terms.options.autocomplete.form).find('input[value="'+$(this).attr('data-termId')+'"][type="hidden"]')
                                    .remove();
                            }
                            // remove the displayed content
                            $(this).remove();
                        });
                        $(Terms.options.autocomplete.selectedContainer).append(_newSelected);
                        
                        // create the input field for this selected item
                        _termIdInput = $('<input type="hidden" name="' + Terms.options.autocomplete.selectedInputName + '[]" value="'+ui.item.id+'" />')
                        $(Terms.options.autocomplete.form).append(_termIdInput);
                    }
                    this.value = '';
                    
                    return false;
                }
            });
            
            return _self;
        }, // end _autocomplete
        _add: function(_self) {
            _form = $(Terms.options.modalAddTerm.form);
            _button = $(Terms.options.modalAddTerm.submit_button);
            
            // bind button click
            _button.click(function(){
                _form.submit();
                
                return false;
            });
            
            // bind form submit
            _form.submit(function(){
                _buttonHtml = _button.html();
                _button.html(_button.attr('data-loading-text')).attr('disabled', true);
                $.ajax({
                    url: _form.attr('action'),
                    data: _form.serialize(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(json) {
                        _button.html(_buttonHtml).attr('disabled', false);
                        hideModal(_self);
                    },
                    error: function() {
                        _button.html(_buttonHtml).attr('disabled', false);
                    }
                });
                return false; 
            });
            
            return _self;
        },// end _add
        // handler for loading current terms of an object thru AJAX
        _loadCurrentTerms: function(_self)
        {
            $.ajax({
                url: Terms.options.loadCurrentTerms.url,
                type: 'get',
                dataType: 'json',
                success: function(json) {
                    _self.html(json.html).find('.autocompleteSelected').bind('click', function(){
                        _el = $(this);
                        _el.hide();
                        $.ajax({
                            url: Terms.options.loadCurrentTerms.removeCurrentTermUrl,
                            data: {'termId': _el.attr('data-termId') },
                            type: 'post',
                            success: function (){
                                _el.remove();
                            },
                            error: function(response) {
                                _el.show();
                                console.log(response);
                            }
                        });
                    });
                },
                // failed loading the list
                error: function() {
                    _self.html('<span style="color:red;" class="error"><small>Error in loading current list of terms</small></span>');
                }
            });
            
            return _self;
        }
    };
    
    Terms.actions = {
        'autocomplete': function(_self){
            return _self.each(function(){
                Terms._autocomplete($(this));
            });
        },
        'modalAddTerm': Terms._add,
        'loadCurrentTerms': function (_self) {
            return _self.each(function(){
                Terms._loadCurrentTerms($(this));
            });
        }
    };
    
    // jquery extension for terms functionalities
    $.fn.terms = function(_action, _options){
        $.extend(Terms.options[_action] || {}, _options);
        
        return Terms.actions[_action]
            ? Terms.actions[_action](this)
            : this;
    };
    
    
})(jQuery);