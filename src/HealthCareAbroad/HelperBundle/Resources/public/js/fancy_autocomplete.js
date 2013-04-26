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
    this.widget = widget;
    this.options = this._initializeOptions(options);
    this.initialize();
};

(function($){
    $.extend(FancyAutocompleteWidget.prototype, {
        
        widget: null,
        
        dropdownTrigger: null,
        
        options: {},
        
        /**
         * Reload the datasource
         * TODO: improve this so we can avoid async false for ajax requests
         * 
         * @access public
         */
        reloadSource: function(){
            this.options.source = this.options.reloadSource();
            
            return this;
        },
        
        disabled: function(disabled){
            var _disabled = (disabled === true || disabled === 1) ;
            
            this.widget.attr('disabled', _disabled);
            this.dropdownTrigger.attr('disabled', _disabled);
            
            return this;
        },
        
        /**
         * Initialize widget
         * 
         * @access public
         */
        initialize: function(){
            
            // reset the values, in case of browser refresh that does not clear form values
            this.resetValue();
            
            // build autocomplete options
            var autocompleteOptions = this._buildAutocompleteOptions(this.options);
            var widget = this.widget;
            this.widget.autocomplete(autocompleteOptions);
            
            // check if widget has a data-autocomplete-trigger attribute
            var _autocompleteTrigger = $(this.widget.attr('data-autocomplete-trigger'));
            if (_autocompleteTrigger.length) {
                this.dropdownTrigger = _autocompleteTrigger;
                this.dropdownTrigger.on('click', function(){
                    widget.autocomplete('search', '');
                });
            }
            
            // check if widget has a data-dropdown
            var _autocompleteDropdown = $(this.widget.attr('data-dropdown'));
            if (_autocompleteDropdown.length){
                this._overrideAutocompleteRendering(_autocompleteDropdown);
            }
            
            return this;
        },
        
        /**
         * Reset current values
         * 
         * @access public
         */
        resetValue: function(){
            this.widget.val('');
            if (this.options.valueContainer){
                this.options.valueContainer.val('');
            }
            
            return this;
        },
        
        /**
         * Override rendering function of jQuery UI autocomplete widget
         * 
         * @access private
         */
        _overrideAutocompleteRendering: function(dropdown) {
            var widget = this.widget;
            var options = this.options;
            var _onSelect = this._autocompleteSelect;
            
            // override _renderItemData
            widget.data('ui-autocomplete')._renderItemData = function(ul, item){
                var _renderedItem = widget.data('ui-autocomplete')._renderItem( ul, item ); 
                return _renderedItem.data( "ui-autocomplete-item", item );
            };
            
            // override _renderItem
            widget.data('ui-autocomplete')._renderItem = function(ul, item) {
                var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+(item.custom_label ? item.custom_label  : item.label)+'</a>');
                _itemLink.on('click', function(){
                    _onSelect(widget, options, item);
                });
                return $("<li>").append(_itemLink).appendTo(ul);
            };
            
            // override _renderMenu
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
        
        // autocomplete.source method, context is ui.autocomplete
        _autocompleteSource: function(request, response){
            var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
            var matches = [];
            // variable "this" refers to the ui.autocomplete widget
            var dataSource = this.element.data('fancyAutocomplete').options.source;
            dataSource.map(function(_i){
                if (_i.id && ( !request.term || matcher.test(_i.label))) {
                    matches.push({'id': _i.id, 'label': _i.label, 'custom_label': _i.custom_label ? _i.custom_label : null });
                }
            });
           response(matches);
        },
        
        _autocompleteChange: function(event, ui){
            if (!ui.item) {
                // variable "this" here refers to the subject of this event, which is the autcomplete widget
                $(this).data('fancyAutocomplete').resetValue();
            }
        },
        
        _autocompleteSelect: function(widget, _options, item) {
            // check that the selected value is not the same as the new selected
            if (_options.valueContainer && _options.valueContainer.val() != item.id) {
                console.log('changed value from '+_options.valueContainer.val()+' to '+item.id);
                _options.valueContainer.val(item.id);
                widget.val(item.label);
                
                // check if their is an onAutocompleteSelectCallback function
                if (_options.onAutocompleteSelectCallback && 'function' == typeof(_options.onAutocompleteSelectCallback)) {
                    _options.onAutocompleteSelectCallback();
                }
            }
        },
        
        // build the options for jQuery autocomplete
        _buildAutocompleteOptions: function(_options){
            // localize variable
            var widget = this.widget;
            var _autocompleteOptions = _options.autocomplete;
            
            // set the source option
            _autocompleteOptions.source = this._autocompleteSource;
            
            // set the change handler
            _autocompleteOptions.change = this._autocompleteChange;
            
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
                reloadSource: function(){}
            };
            
            // merge default options and the passed options
            var _newOpts = $.extend(true, _defaults, _options);
            
            // non-existent valueContainer obect  
            if ('object' == typeof _newOpts.valueContainer && 0 == _newOpts.valueContainer.length) {
                _newOpts.valueContainer = null;
            }
            
            // check if there is a source option given
            // we will build source option as an object
            if (_newOpts.source) {
                switch (typeof _newOpts.source) {
                    case 'string':
                        // source is a string, we will assume that it is a url and we fetch thru ajax
                        // this is limited to being async false since we are waiting for the return value to be set as the value of _newOpts.source
                        var _url = _newOpts.source;
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
                        _newOpts.source = _source;
                        break;
                    case 'function':
                        // set source as the return value of the function
                        _newOpts.source = _newOpts.source();
                        break;
                    case 'object':
                        // source is already an object
                        break;
                    default:
                        // unrecognized, return a function with empty data as return value
                        return [];
                        break;
                }
            }
            // no source submitted
            else {
                // return an empty array
                _newOpts.source = [];
            }
            
            // validate that custom reloadSource is a function
            if ('function' != typeof _newOpts.reloadSource) {
                _newOpts.reloadSource = function(){}; // set to empty default function
            }
            
            return _newOpts;
        }// end _initializeOptions
    });
    
    
    $.fn.fancyAutocomplete = function(_options) {
        return this.each(function(){
            var _myWidget = new FancyAutocompleteWidget($(this), _options);
            $(this).data('fancyAutocomplete', _myWidget);
        });
    };
    
})(jQuery);