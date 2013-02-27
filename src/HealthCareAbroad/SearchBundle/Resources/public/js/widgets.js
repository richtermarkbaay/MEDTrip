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
            $(BroadSearchWidget.formComponents[_theOtherType].valueField).val(0);
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
                   select: function(event, ui) {
                       // reload the other sources
                       BroadSearchWidget.loadSourcesByType(ui.item);
                       // set the values
                       $(BroadSearchWidget.formComponents[ui.item.type].valueField).val(ui.item.id);
                       BroadSearchWidget.submitButton.attr('disabled', false);
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
                   }
                });
            
            componentOptions.autocompleteField.data('ui-autocomplete')._renderItem = function(ul, item) {
                var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
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
}
