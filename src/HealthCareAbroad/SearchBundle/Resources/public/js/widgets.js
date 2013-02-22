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
            
            $.ajax({
                url: BroadSearchWidget.sourceUri,
                // replace the type since we will be updating the dataSource for the other type
                data: {type: (_type == 'treatments' ? 'destinations' : 'treatments'), 'value': params.value, 'label': params.label}, 
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
            
            /*componentOptions.mainInputField.typeahead({
                minLength: 1, 
                source: function(query, process){
                    var src = BroadSearchWidget.formComponents[type].dataSource;
                    
                    
                }
            );*/
            componentOptions.mainInputField.change(function(){
                // value has changed from what was selected from the list
                var _val = $.trim($(this).val());
                if ( _val != BroadSearchWidget.formComponents[type].selectedLabel) {
                    $(BroadSearchWidget.formComponents[type].valueField).val('0');
                }
                if (!_val.length) {
                    // check if the other type_in inputs are empty
                    var _emptyTextboxesLen = BroadSearchWidget.form.find(BroadSearchWidget.inputboxCommonClass+':text[value=""]').length;
                    var _allTextboxesLen = BroadSearchWidget.form.find(BroadSearchWidget.inputboxCommonClass+':text').length;
                    BroadSearchWidget.submitButton.attr('disabled', _emptyTextboxesLen == _allTextboxesLen);
                }
                $(this).val(_val); // just incase there are trailing whitespaces                
                
            })
            .change();
            
            componentOptions.dropdownButton.click(function(){
                componentOptions.autocompleteField.autocomplete('search', '');
            });
            
            componentOptions.autocompleteField
                .click(function(e){
                    e.stopPropagation();
                })
                // setup autocomplete
                .autocomplete({
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
                                matches.push($.extend(_i, {'type': type}));
                            }
                        });
                       response(matches);
                   }
                })
                .data('ui-autocomplete')._renderMenu = function(ul, data){
                    ul.remove();
                    var _listContainer = $(this.element.attr('data-container'));
                    _listContainer.html('');
                    $.each(data, function(index, item){
                        var _itemLink = $('<a data-value="'+item.id+'" data-type="'+item.type+'">'+item.label+'</a>');
                        _itemLink.bind('click', function(){
                            // set value to form
                            var _this = $(this);
                            // reload the other sources
                            BroadSearchWidget.loadSourcesByType(item);
                            // set the value fields
                            BroadSearchWidget.formComponents[item.type].mainInputField.val(item.label);
                            $(BroadSearchWidget.formComponents[item.type].valueField).val(item.value);
                            BroadSearchWidget.formComponents[item.type].selectedLabel = item.label;
                            // enable the button
                            BroadSearchWidget.submitButton.attr('disabled', false);
                        });
                        return $('<li>').append(_itemLink).appendTo(_listContainer);
                    })
                };
        });
    },
    
    initializeForm: function(form, components){
        
        BroadSearchWidget.form = form;
        BroadSearchWidget.submitButton = form.find('button[type="submit"]');
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
