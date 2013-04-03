/**
 * Autocomplete widget that combines jQuery autocomplete widget and bootstrap dropdown
 * 
 * @author Allejo Chris Velarde
 * April 02, 2013
 */

/**
 * Available options:
 * {
 *      // Data source, can either be a function, string, or an object. If string this will be assumed as URL
 *      source: {},
 *      // jQuery autocomplete options
 *      autocomplete: {** autocomplete.source option will be overridden by passed source option see options in jQuery autocomplete @see http://api.jqueryui.com/autocomplete/ **},
 *      // maximum number of items to display
 *      maxItems: 50,
 *      // DOM element container of the selected item value
 *      valueContainer: null,
 *      // callback function for jQuery UI autocomplete select callback
 *      onAutocompleteSelectCallback: function(){},
 * }
 */
var FancyAutocompleteWidget = function(widget, options){
    this.initialize(widget, options);
};

(function($){
    $.extend(FancyAutocompleteWidget.prototype, {
        
        widget: null,
        
        initialize: function(widget, options){
            
            options = this._initializeOptions(options);
            
            this.widget = widget;
            
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
                this._overrideAutocompleteRendering(_autocompleteDropdown, options);
            }
        },
        
        _overrideAutocompleteRendering: function(dropdown, options) {
            var widget = this.widget;
            widget.data('ui-autocomplete')._renderItemData = function(ul, item){
                var _renderedItem = widget.data('ui-autocomplete')._renderItem( ul, item ); 
                return _renderedItem.data( "ui-autocomplete-item", item );
            };
            
            var _onSelect = this._autocompleteSelect;
            widget.data('ui-autocomplete')._renderItem = function(ul, item) {
                var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
                _itemLink.on('click', function(){
                    _onSelect(widget, options, item);
                });
                return $("<li>").append(_itemLink).appendTo(ul);
                //return $("<li>"+item.label+"</li>").appendTo(ul);
            };
            
            
            widget.data('ui-autocomplete')._renderMenu = function(ul, data){
                if (!dropdown.parent().hasClass('open')) {
                    dropdown.dropdown('toggle');
                }
                var _cnt = 0;
                $.each( data, function( index, item ) {
                    if (_cnt > options.maxItems) {
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
        
        _autocompleteSelect: function(widget, _options, item) {
            // check that the selected value is not the same as the new selected
            if (_options.valueContainer && _options.valueContainer.val() != item.id) {
                console.log('changed value from '+_options.valueContainer.val()+' to '+item.id);
                _options.valueContainer.val(item.id);
                widget.val(item.label);
                // check if their is an onAutocompleteSelectCallback function
            }
        },
        
        _buildAutocompleteOptions: function(_options){
            
            var widget = this.widget;
            var _autocompleteOptions = _options.autocomplete;
            
            _autocompleteOptions.source = _options.source();
            
            // there is a custom handler for autocomplete.select event
            if (_options.autocomplete.select && 'function' == typeof _options.autocomplete.select) {
                _autocompleteOptions.select = _options.autocomplete.select;
            }
            else {
                var _onSelect = this._autocompleteSelect;
                // use default handler for autocomplete.select event
                _autocompleteOptions.select = function(event, ui){
                    _onSelect(widget, _options, ui.item);
                }
            }
            
            return _autocompleteOptions;
        },
        
        _initializeOptions: function(_options) {
            var _defaults = {
                autocomplete: {
                    minLength: 0,
                    delay: 0
                },
                maxItems: 50,
                valueContainer: null,
            };
            
            // merge default options and the passed options
            var _newOpts = $.extend(true, _defaults, _options);
            
            // non-existent valueContainer obect  
            if ('object' == typeof _newOpts.valueContainer && 0 == _newOpts.valueContainer.length) {
                _newOpts.valueContainer = null;
            }
            else {
                // reset value, in cases of refresh browsers where value is retained
                _newOpts.valueContainer.val('');
            }
            
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