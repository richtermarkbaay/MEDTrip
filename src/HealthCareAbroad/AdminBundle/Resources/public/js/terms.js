
(function($){
    
    function split( val ) {
        return val.split( /,\s*/ );
    }
    
    var Terms = {
        'options': {
            'autocomplete': {
                'minLength': 1,
                'type': 0,
                'remoteUrl': '',
                'selectedContainer': '.autocompleteSelectedContainer'
            }
        },
        // handler for autocomplete action
        _autocomplete: function(_self) {
            
            var _currentTerms = [];
            
            _self.bind('keydown', function (event){
                if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ){
                    event.preventDefault();
                }
            }).autocomplete({
                minLength: Terms.options.autocomplete.source,
                source: function (request, response) {
                    $.ajax({
                        url: Terms.options.autocomplete.remoteUrl,
                        data: { type:Terms.options.autocomplete.type, 'selectedTems': _currentTerms },
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
                        _newSelected = $(ui.item.html);
                        _newSelected.bind('click', function(){
                            _index = _currentTerms.indexOf($(this).html());
                            if (_index >= 0 ){
                                _currentTerms.splice(_index, 1); // remove from _currentTerms
                            }
                            $(this).remove();
                        });
                        $(Terms.options.autocomplete.selectedContainer).append(_newSelected);
                    }
                    this.value = '';
                    /**
                    _currentTerms = split( this.value );
                    // remove the current input
                    _currentTerms.pop();
                    // value has not been pushed yet
                    if (_currentTerms.indexOf(ui.item.value)< 0) {
                        // add the selected item
                        _currentTerms.push( ui.item.value );
                    }
                    this.value = _currentTerms.join( ", " ).replace(/,\s*$/,'');
                    **/
                    return false;
                }
            });
            
            return _self;
        }
    };
    
    Terms.actions = {
        'autocomplete': Terms._autocomplete
    };
    
    // jquery extension for terms functionalities
    $.fn.terms = function(_action, _options){
        $.extend(Terms.options[_action] || {}, _options);
        
        return Terms.actions[_action]
            ? Terms.actions[_action](this)
            : this;
    };
    
    
})(jQuery);