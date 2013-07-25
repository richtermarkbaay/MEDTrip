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
            'selectedLabel': ''
        },
        destinations: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'valueField': '#destination_id',
            'selectedLabel': ''
        }
    },

    setSourceUri: function(_v){
        this.sourceUri = _v;
        return this;
    },

    loadSourcesByType: function(params) {
        var _type = typeof params.type === 'undefined' ? null : params.type;

        if (_type != 'treatments' || _type != 'destinations') {
            var _theOtherType = _type == 'treatments' ? 'destinations' : 'treatments';

            // reset first the hidden value field of the other field
            if ('' == $.trim(BroadSearchWidget.formComponents[_theOtherType].autocompleteField.val())) {
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

            var listWrapper = componentOptions.autocompleteField.siblings('.combolist-wrapper:first');

            var getLink = function(item) {
                /*TODO: use classes to format the labels*/
                var link;
                if (item.type == 'destinations') {
                    link = item.id.slice(-2) == '-0' ?
                        '<a data-value="'+item.id+'" data-type="'+item.type+'"><b>'+item.label+'<b/></a>':
                        '<a data-value="'+item.id+'" data-type="'+item.type+'">&nbsp;&nbsp;'+item.label+'</a>'
                    ;
                } else if (item.type == 'treatments') {
                    link = '<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>';
                }

                return link;
            };

            componentOptions.autocompleteField
                // setup autocomplete
                .autocomplete({
                    delay: 0,
                    minLength: 0,
                    appendTo : listWrapper,
                    response: function(event, ui) {
                        if (ui.content.length > 0) {
                            listWrapper.removeClass('hide');
                        }
                    },
                    source:  function(request, response) {
                        var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
                        var matches = [];
                        var submatches = [];

                        $.each(componentOptions.dataSource, function(_i, _each){
                            if (_each.value && ( !request.term || matcher.test(_each.label))) {
                                var _eachLabel = _each.label;

                                if (type == 'destinations') {
                                    if (_each.value.slice(-2) == '-0') {
                                        submatches[_each.value] = _each.value;
                                    }

                                    $.each(componentOptions.dataSource, function(i, subeach) {
                                        var subeachValue = subeach.value;
                                        if (subeachValue == _each.value.split('-')[0] + '-0') {
                                            if (typeof submatches[subeachValue] == 'undefined') {
                                                submatches[subeachValue] = subeachValue;
                                                matches.push({'value': subeach.label, 'id': subeach.value, 'label': subeach.label, 'type': type});
                                                return false;
                                            }
                                        }
                                    });

                                   _eachLabel = _eachLabel.split(',').reverse().splice(-1).join(',');
                                }

                                matches.push({'value': _each.label, 'id': _each.value, 'label': _eachLabel, 'type': type});
                            }
                        });
                        response(matches);
                   },
                   select: function(event, ui) {
                       if ($(BroadSearchWidget.formComponents[type].valueField).val()  != ui.item.id) {
                           // load sources of the other type
                           BroadSearchWidget.loadSourcesByType(ui.item);
                           $(BroadSearchWidget.formComponents[ui.item.type].valueField).val(ui.item.id);
                       }
                   },
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
                   },
                   open: function(event, ui) {
                       $('.ui-autocomplete').css('width','auto');
                   },
                   close: function(event, ui) {
                       listWrapper.addClass('hide');
                   }
                });

            componentOptions.autocompleteField.data('ui-autocomplete')._renderItem = function(ul, item) {
                /*var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');*/
                var _itemLink = $(getLink(item));

                _itemLink.on('click', function(){
                    BroadSearchWidget.loadSourcesByType(item);
                    $(BroadSearchWidget.formComponents[item.type].valueField).val(item.id);
                    BroadSearchWidget.submitButton.attr('disabled', false);
                    BroadSearchWidget.formComponents[item.type].autocompleteField.val(item.label);

                });
                return $("<li>").append(_itemLink).appendTo(ul);
            };

            componentOptions.autocompleteField.data('ui-autocomplete')._renderItemData = function (ul, item) {
                var _renderedItem = componentOptions.autocompleteField.data('ui-autocomplete')._renderItem( ul, item );
                return _renderedItem.data( "ui-autocomplete-item", item );
            };
            //ui-menu-item
            componentOptions.autocompleteField.data('ui-autocomplete')._renderMenu = function(ul, data) {
                var _cnt = 0;
                $.each( data, function( index, item ) {
                    if (_cnt > 50) {
                        return false;
                    }
                    componentOptions.autocompleteField.data('ui-autocomplete')._renderItemData( ul, item );
                    _cnt++;
                });
            }
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
                source: function(request, response) {
                   var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i");
                   var matches = [];
                   $.each(NarrowSearchWidget.sources[widget_key], function(_i, _val) {
                       if (_val.value && ( !request.term || matcher.test(_val.label))) {
                           matches.push({value: _val.label, id: _val.value, label: _val.label, type: widget_key});
                       }
                   });

                   response(matches);
                },
                open: function(event, ui) {
                    $('.ui-autocomplete').css('width','auto');
                },
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
