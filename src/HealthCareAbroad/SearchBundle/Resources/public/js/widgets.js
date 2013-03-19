/**
 * @author Allejo Chris Velarde
 */

var BroadSearchWidget = {
    sourceUri: '',
    
    form: null,
    
    submitButton: null,
    
    inputboxCommonClass: '.type_in',
    
    formComponents: {
        treatments: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'valueField': '#treatment_id',
            'selectedLabel': '',
        },
        destinations: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'valueField': '#destination_id',
            'selectedLabel': '',
        }
    },
    
    setSourceUri: function(_v){
        this.sourceUri = _v;       
        return this;
    },
    
    loadSourcesByType: function(params) {
        var _type = params.type || null;
        if (_type != 'treatments' || _type != 'destinations') {
            var _theOtherType = _type == 'treatments' ? 'destinations' : 'treatments';
            // reset first the hidden value field of the other field
            if ('' == BroadSearchWidget.formComponents[_theOtherType].autocompleteField.val().trim()) {
                $(BroadSearchWidget.formComponents[_theOtherType].valueField).val(0);
            }
            
            // reset dataSource of the other type
            BroadSearchWidget.formComponents[_theOtherType].dataSource = {};
            
            $.ajax({
                url: BroadSearchWidget.sourceUri,
                // replace the type since we will be updating the dataSource for the other type
                data: {type: _theOtherType, 'value': params.id, 'label': params.label}, 
                dataType: 'json',
                success: function(response) {
                    // update the value of the dataSource for this type
                    $.each(response, function(_key, _data){
                        BroadSearchWidget.formComponents[_key].dataSource = _data;
                    });
                    
                }
             });
        }
    },
    
    initializeComponents: function(){
        
        $.each(BroadSearchWidget.formComponents, function(type, componentOptions){
            
            componentOptions.dropdownButton.click(function(){
                componentOptions.autocompleteField.autocomplete('search', '');
            });
            
            componentOptions.autocompleteField
                // setup autocomplete
                .autocomplete({
                    appendTo: componentOptions.dropdown,
                    delay: 0,
                    minLength: 0,
                    response: function(event, ui) {
                        if (ui.content.length == 0) {
                            $($(this).attr('data-container')).html(''); // clear list
                        }
                    },
                    source:  function(request, response) {
                        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                        var matches = [];
                        componentOptions.dataSource.map(function(_i){
                            if (_i.value && ( !request.term || matcher.test(_i.label))) {
                                matches.push({'value': _i.label, 'id': _i.value, 'label': _i.label, 'type': type});
                            }
                        });
                       response(matches);
                   },
                   /*select: function(event, ui) {
                       // reload the other sources
                       BroadSearchWidget.loadSourcesByType(ui.item);
                       // set the values
                       $(BroadSearchWidget.formComponents[ui.item.type].valueField).val(ui.item.id);
                       BroadSearchWidget.submitButton.attr('disabled', false);
                   },*/
                   change: function(event, ui) {
                       if (!ui.item) {
                           $(BroadSearchWidget.formComponents[type].valueField).val(0);
                           // check if this field allows an empty value
                           var _allowEmpty = $(this).attr('data-allow-empty') ? Boolean($(this).attr('data-allow-empty')) : false;
                           if (_allowEmpty) {
                               $(this).val('');
                           }
                       }
                       
                       // check if both type-in values are empty
                       var _allEmpty = BroadSearchWidget.form.find('input.type_in:text[value=""]').length >= BroadSearchWidget.form.find('input.type_in:text').length;
                       BroadSearchWidget.submitButton.attr('disabled', _allEmpty);
                   }
                });
            
            componentOptions.autocompleteField.data('ui-autocomplete')._renderItem = function(ul, item) {
                var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
                _itemLink.on('click', function(){
                    BroadSearchWidget.loadSourcesByType(item);
                    $(BroadSearchWidget.formComponents[item.type].valueField).val(item.id);
                    BroadSearchWidget.formComponents[item.type].autocompleteField.val(item.label);
                    BroadSearchWidget.submitButton.attr('disabled', false);
                });
                return $("<li>").append(_itemLink).appendTo(ul);
            };
            
            componentOptions.autocompleteField.data('ui-autocomplete')._renderItemData = function (ul, item) {
                var _renderedItem = componentOptions.autocompleteField.data('ui-autocomplete')._renderItem( ul, item ); 
                return _renderedItem.data( "ui-autocomplete-item", item ); 
            };
            //ui-menu-item
            componentOptions.autocompleteField.data('ui-autocomplete')._renderMenu = function(ul, data) {
                if (!componentOptions.dropdown.parent().hasClass('open')) {
                    componentOptions.dropdown.dropdown('toggle');
                }
                var _cnt = 0;
                $.each( data, function( index, item ) {
                    if (_cnt > 50) {
                        return false;
                    }
                    componentOptions.autocompleteField.data('ui-autocomplete')._renderItemData( ul, item );
                    _cnt++;
                });
                ul.attr('class', 'popup-list').attr('style', ''); 
            }
            
            // override suggest function not to resize and reposition menu
            componentOptions.autocompleteField.data('ui-autocomplete')._suggest = function( items ) {
                var ul = this.menu.element.empty();
                this._renderMenu( ul, items );
                this.menu.refresh();
                ul.show();

                if ( this.options.autoFocus ) {
                    this.menu.next();
                }
            };
            
            componentOptions.autocompleteField.data('ui-autocomplete')._resizeMenu = function() {
                console.log('do not resize menu');
            };
        });
    },
    
    initializeForm: function(form, components){
        
        BroadSearchWidget.form = form;
        BroadSearchWidget.submitButton = form.find('button[type="submit"]');
        BroadSearchWidget.submitButton.attr('disabled', true);
        $.extend(true, BroadSearchWidget.formComponents, components);
        
        $.ajax({
            url: BroadSearchWidget.sourceUri,
            data: {type:"all"},
            dataType: 'json',
            success: function(response) {
                BroadSearchWidget.formComponents.treatments.dataSource = response.treatments;
                BroadSearchWidget.formComponents.destinations.dataSource = response.destinations;
                BroadSearchWidget.initializeComponents();
            }
         });
        
        return this;
    }
}/** end of BroadSearchWidget **/


var NarrowSearchWidget = {
    form: null,
    searchParameterName: 'searchParameter',
    sourceUri: '',
    sources: {},
    setSourceUri: function(_v){
        this.sourceUri = _v;
        return this;
    },
    
    updateSource: function(widget_key){
        NarrowSearchWidget.form.find('input[name="filter"]').val(widget_key);
        $.ajax({
            url: NarrowSearchWidget.sourceUri,
            data: NarrowSearchWidget.form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                NarrowSearchWidget.sources[widget_key] = response;
            }
        })
    },
    
    initializeForm: function(_form, _options){
        this.form = _form;
        // setup the sources that will be used by autocomplete
        $.each(_options.widget_keys, function(_index, widget_key){
            NarrowSearchWidget.updateSource(widget_key);
        });
        
        if (_options.dropdown_triggers) {
            this.__initFromButtonTriggers(_options.dropdown_triggers);
        }
        return this;
    },
    __initFromButtonTriggers: function (buttonTriggers) {
        $.each(buttonTriggers, function(){
            var button = $(this);
            var dropdown = $(button.attr('data-dropdown'));
            var autocompleteField = $(dropdown.attr('data-autocomplete'));
            // init autocompete
            NarrowSearchWidget.__initAutocomplete(autocompleteField, dropdown ,button.attr('data-widget-key'));
            button.click(function(){
                autocompleteField.autocomplete('search', '');
            });
        });
    },
    
    __initAutocomplete: function(field, dropdown, widget_key){
        var dropdown = dropdown;
        field.click(function(e){
                e.stopPropagation();
            })
            .autocomplete({
                appendTo: dropdown,
                delay: 0,
                minLength: 0,
                source:  function(request, response) {
                   var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                   var matches = [];
                   NarrowSearchWidget.sources[widget_key].map(function(_i){
                       if (_i.value && ( !request.term || matcher.test(_i.label))) {
                           matches.push({'value': _i.label, 'id': _i.value, 'label': _i.label, 'type': widget_key});
                       }
                   });
                  response(matches);
              }
            });
        // override _renderItem function of UI.autocomplete
        field.data('ui-autocomplete')._renderItem = function(ul, item) {
            var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
            _itemLink.on('click', function(){
                $(field.attr('data-value-container')).html(item.label); // update the label of the value container
                var _searchInputName = NarrowSearchWidget.searchParameterName+'['+item.type+']';
                var _searchParamInput = NarrowSearchWidget.form.find('input[name="'+_searchInputName+'"]');
                if (_searchParamInput.length) {
                    _searchParamInput.val(item.id);
                }
                else {
                    // append new input element
                    $('<input type="hidden" name="'+_searchInputName+'" value="'+item.id+'"/>').appendTo(NarrowSearchWidget.form);
                }
                
                // reload the other sources
                $.each(NarrowSearchWidget.sources, function(_key, source){
                   if (_key != item.type) {
                       NarrowSearchWidget.updateSource(_key);
                   } 
                });
            });
            return $("<li>").append(_itemLink).appendTo(ul);
        };
        // override _renderItemData
        field.data('ui-autocomplete')._renderItemData = function (ul, item) {
            var _renderedItem = field.data('ui-autocomplete')._renderItem( ul, item ); 
            return _renderedItem.data( "ui-autocomplete-item", item ); 
        };
        //ui-menu-item
        field.data('ui-autocomplete')._renderMenu = function(ul, data) {
            if (!dropdown.parent().hasClass('open')) {
                dropdown.dropdown('toggle');
            }
            var _cnt = 0;
            $.each( data, function( index, item ) {
                if (_cnt > 50) {
                    return false;
                }
                field.data('ui-autocomplete')._renderItemData( ul, item );
                _cnt++;
            });
            ul.attr('class', 'popup-list').attr('style', ''); 
        };
        
        // override suggest function not to resize and reposition menu
        field.data('ui-autocomplete')._suggest = function( items ) {
            var ul = this.menu.element.empty();
            this._renderMenu( ul, items );
            this.menu.refresh();
            ul.show();

            if ( this.options.autoFocus ) {
                this.menu.next();
            }
        };
    }
}
