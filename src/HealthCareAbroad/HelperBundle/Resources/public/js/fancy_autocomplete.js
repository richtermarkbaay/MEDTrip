/**
 * Autocomplete widget that combines jQuery autocomplete widget and bootstrap dropdown
 * 
 * @author Allejo Chris Velarde
 * April 02, 2013
 */

var FancyAutocompleteWidget = function(widget, options){
    this.initialize(widget, options);
};

(function($){
    $.extend(FancyAutocompleteWidget.prototype, {
        
        widget: null,
        
        maxItems: 0,
        
        initialize: function(widget, options){
            
            options = this._initializeOptions(options);
            
            this.widget = widget;
            this.maxItems = options.maxItems;
            
            // build autocomplete options
            var autocompleteOptions = this._buildAutocompleteOptions(options);
            
            this.widget.autocomplete(autocompleteOptions);
            
            // check if widget has a data-autocomplete-trigger attribute
            var _autocompleteTrigger = $(this.widget.attr('data-autocomplete-trigger'));
            if (_autocompleteTrigger.length) {
                _autocompleteTrigger.on('click', function(){
                    widget.autocomplete('search', '');
                });
            }
            
            // check if widget has a data-dropdown
            var _autocompleteDropdown = $(this.widget.attr('data-dropdown'));
            if (_autocompleteDropdown.length){
                this._overrideAutocompleteRendering(widget, _autocompleteDropdown);
            }
        },
        
        _overrideAutocompleteRendering: function(widget, dropdown) {
            
            this.widget.data('ui-autocomplete')._renderItemData = function(ul, item){
                var _renderedItem = widget.data('ui-autocomplete')._renderItem( ul, item ); 
                return _renderedItem.data( "ui-autocomplete-item", item );
            };
            
            this.widget.data('ui-autocomplete')._renderItem = function(ul, item) {
                var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
                return $("<li>").append(_itemLink).appendTo(ul);
                //return $("<li>"+item.label+"</li>").appendTo(ul);
            };
            
            var maxItems = this.maxItems;
            this.widget.data('ui-autocomplete')._renderMenu = function(ul, data){
                if (!dropdown.parent().hasClass('open')) {
                    dropdown.dropdown('toggle');
                }
                var _cnt = 0;
                $.each( data, function( index, item ) {
                    if (_cnt > maxItems) {
                        return false;
                    }
                    widget.data('ui-autocomplete')._renderItemData( ul, item );
                    _cnt++;
                });
                ul.attr('class', 'popup-list').attr('style', '');
                ul.appendTo(dropdown);
            };
        },
        
        _autocompleteSource: function(request, response){
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            var matches = [];
            FancyAutocompleteWidget.prototype.dataSource.map(function(_i){
                if (_i.value && ( !request.term || matcher.test(_i.label))) {
                    matches.push({'value': _i.label, 'id': _i.value, 'label': _i.label, 'type': type});
                }
            });
           response(matches);
        },
        
        _buildAutocompleteOptions: function(_options){
            
            var _autocompleteOptions = _options.autocomplete;
            _autocompleteOptions.source = _options.source();
            
            return _autocompleteOptions;
        },
        
        _initializeOptions: function(_options) {
            var _defaults = {
                autocomplete: {
                    minLength: 0,
                    delay: 0
                },
                maxItems: 50
            };
            
            // merge default options and the passed options
            var _newOpts = $.extend(true, _defaults, _options);
            
            // check if there is a source option given
            // we will build source option as a function
            if (_newOpts.source) {
                switch (typeof _newOpts.source) {
                    case 'string':
                        // source is a string, we will assume that it is a url and we fetch thru ajax
                        // this is limited to being async false since we are converting _newOpts.source into a function that returns the source data object
                        var _url = _newOpts.source;
                        _newOpts.source = function(){
                            var _source = {};
                            $.ajax({
                               url: _url,
                               type: 'get',
                               dataType: 'json',
                               async: false,
                               success: function(_response) {
                                   _source = _response;
                               },
                               error: function() {
                                   _source = [];
                               }
                            });
                            return _source;
                        };
                        break;
                    case 'object':
                        // source is already an object, convert it to a function that returns it
                        var _obj = _newOpts.source;
                        _newOpts.source = function() {
                            return _obj;
                        }
                        break;
                    case 'function':
                        // source is already a function, all we have to do is trust that it returns a data object
                        break;
                    default:
                        // unrecognized, return a function with empty data as return value
                        _newOpts.source = function() {
                            return [];
                        }
                        break;
                }
            }
            // no source submitted
            else {
                // return a function with empty data as return value
                _newOpts.source = function() {
                    return [];
                }
            }
            
            return _newOpts;
        }// end _initializeOptions
    });
    
    
    $.fn.fancyAutocomplete = function(_options) {
        return this.each(function(){
            var _myWidget = new FancyAutocompleteWidget($(this), _options);
            //FancyAutocomplete._initializeWidget($(this), _options);
        });
    };
    
})(jQuery);