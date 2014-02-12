/**
 * @author Allejo Chris Velarde
 */

var BroadSearchWidget = {
    sourceUri: '',
    preloadedDestinations: null,
    preloadedTreatments: null,
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
            var listWrapper = BroadSearchWidget.formComponents[_theOtherType].autocompleteField.siblings('.combolist-wrapper:first')
            var updateList = !listWrapper.hasClass('hide');
            if (updateList) {
                //TODO: is this the proper way?
                listWrapper.addClass('hide');
            }

            $.ajax({
                url: BroadSearchWidget.sourceUri,
                // replace the type since we will be updating the dataSource for the other type
                data: {type: _theOtherType, 'value': params.id, 'label': params.label},
                dataType: 'json',
                success: function(response) {
                    // update the value of the dataSource for this type
                    $.each(response, function(_key, _data){
                        if (typeof BroadSearchWidget.formComponents[_key] == 'undefined') {
                            return true;
                        }
                        BroadSearchWidget.formComponents[_key].dataSource = _data;
                        if (updateList) {
                            //TODO: verify with Hazel if we want the list to redisplay
                            BroadSearchWidget.formComponents[_key].dropdownButton.click();
                        }
                    });
                }
             });
        }
    },

    initializeComponents: function(){

        $.each(BroadSearchWidget.formComponents, function(type, componentOptions){

            var listWrapper = componentOptions.autocompleteField.siblings('.combolist-wrapper:first');

            componentOptions.dropdownButton.click(function(){
                if (listWrapper.hasClass('hide')){
                    componentOptions.autocompleteField.autocomplete('search', '');
                }
                else {
                    listWrapper.addClass('hide');
                }
            });

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
                        var matcher = new RegExp(SearchWidgetUtils.stripAccents($.ui.autocomplete.escapeRegex(request.term)), "i" );
                        var matches = SearchWidgetUtils.getMatches(matcher, componentOptions.dataSource, type, request);

                        response(matches);
                   },
                   select: function(event, ui) {
                       if ($(BroadSearchWidget.formComponents[type].valueField).val()  != ui.item.id) {
                           // load sources of the other type
                           BroadSearchWidget.loadSourcesByType(ui.item);
                           $(BroadSearchWidget.formComponents[ui.item.type].valueField).val(ui.item.id);
                           BroadSearchWidget.submitButton.attr('disabled', false);
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
                var _itemLink = SearchWidgetUtils.getLink(item);

                /**_itemLink.on('click', function(){
                    BroadSearchWidget.loadSourcesByType(item);
                    $(BroadSearchWidget.formComponents[item.type].valueField).val(item.id);
                    BroadSearchWidget.submitButton.attr('disabled', false);
                    BroadSearchWidget.formComponents[item.type].autocompleteField.val(item.label);

                    console.log('Selected '+item.label);
                });**/
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
                    if (_cnt > 100) {
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

        if (BroadSearchWidget.preloadedDestinations && BroadSearchWidget.preloadedTreatments) {
            BroadSearchWidget.formComponents.treatments.dataSource = BroadSearchWidget.preloadedTreatments;
            BroadSearchWidget.formComponents.destinations.dataSource = BroadSearchWidget.preloadedDestinations;
            BroadSearchWidget.initializeComponents();
        } else {
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
        }

        return this;
    }
};/** end of BroadSearchWidget **/


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
                   var matcher = new RegExp(SearchWidgetUtils.stripAccents($.ui.autocomplete.escapeRegex(request.term)), "i");
                   var matches = SearchWidgetUtils.getMatches(matcher, NarrowSearchWidget.sources[widget_key], widget_key, request);

                   response(matches);
                },
                open: function(event, ui) {
                    $('.ui-autocomplete').css('width','auto');
                },
            });
        // override _renderItem function of UI.autocomplete
        field.data('ui-autocomplete')._renderItem = function(ul, item) {
            /*var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');*/
            var _itemLink = SearchWidgetUtils.getLink(item);

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
};

var SearchWidgetUtils = (function() {
    // String containing replacement characters for stripping accents
    var stripString = 'AAAAAAACEEEEIIII' + 'DNOOOOO.OUUUUY..' + 'aaaaaaaceeeeiiii' + 'dnooooo.ouuuuy.y' + 'AaAaAaCcCcCcCcDd' + 'DdEeEeEeEeEeGgGg' + 'GgGgHhHhIiIiIiIi' + 'IiIiJjKkkLlLlLlL' + 'lJlNnNnNnnNnOoOo' + 'OoOoRrRrRrSsSsSs' + 'SsTtTtTtUuUuUuUu' + 'UuUuWwYyYZzZzZz.';

    var stripAccents = function(str) {
        for (var i = 0; i < str.length; i++) {
            var char = str[i];
            var charIndex = char.charCodeAt(0) - 192; // Index of character code in the strip string
            if (charIndex >= 0 && charIndex < stripString.length) {
                // Character is within our table, so we can strip the accent...
                var outChar = stripString.charAt(charIndex);
                // ...unless it was shown as a '.'
                if (outChar != '.') {
                    str = str.substring(0, i) + outChar + str.substring(i + 1, str.length);
                }
            }
        }
        return str;
    };

    //This is sometimes faster in other browsers
    var stripAccents2 = function (str) {
        var stripped = '';
        for (var i = 0; i < str.length; i++) {
            var char = str[i];
            var charIndex = char.charCodeAt(0) - 192;
            if (charIndex >= 0 && charIndex < stripString.length) {
                var outChar = stripString.charAt(charIndex);
                if (outChar != '.') {
                    char = outChar;
                }
            }
            stripped += char;
        }
        return stripped;
    };

    var isCountry = function(id) {
        return id.slice(-2) == '-0';
    };

    var getLink = function(item) {
        var label = item.type == 'destinations' ? (isCountry(item.id) ? '<b>'+item.label+'<b/>' : '&nbsp;&nbsp;'+item.label) : item.label;

        return $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+label+'</a>');
    };

    var getMatches = function(matcher, datasource, type, request) {
        var matches = [];
        var submatches = [];
        $.each(datasource, function(_i, _each) {
            /* TODO: why do we need to test request.term? */
            if (_each.value && ( !request.term || matcher.test(stripAccents(_each.label)))) {
                var _eachLabel = _each.label;
                if (type == 'destinations') {
                    if (isCountry(_each.value)) {
                        if (typeof submatches[_eachLabel] != 'undefined') {
                            return true;
                        }
                        submatches[_eachLabel] = _each.value;
                        matches.push({'value': _each.label, 'id': _each.value, 'label': _eachLabel, 'type': type});
                        return true;
                    }
                    /* TODO: might be faster if we make a copy of the array then pop items off */
                    for (var i = 0, l = datasource.length; i < l; i++) {
                        var subeach = datasource[i];
                        if (typeof submatches[subeach.label] == 'undefined') {
                            var subeachValue = subeach.value;
                            if (subeachValue == _each.value.split('-')[0] + '-0') {
                                submatches[subeach.label] = subeachValue;
                                matches.push({'value': subeach.label, 'id': subeach.value, 'label': subeach.label, 'type': type});
                                break;
                            }
                        }
                    }
                   _eachLabel = _eachLabel.split(',').reverse().splice(-1).join(',');
                }
                matches.push({'value': _each.label, 'id': _each.value, 'label': _eachLabel, 'type': type});
            }
        });

        return matches;
    };

    return {
        getLink: getLink,
        getMatches: getMatches,
        stripAccents: stripAccents
    };
})();
