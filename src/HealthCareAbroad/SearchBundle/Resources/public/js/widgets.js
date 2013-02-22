/**
 * @author Allejo Chris Velarde
 */

var BroadSearchWidget = {
    sourceUri: '',
    
    form: null,
    
    formComponents: {
        treatments: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'valueField': '#treatment_id',
        },
        destinations: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'valueField': '#destination_id'
        }
    },
    
    setSourceUri: function(_v){
        this.sourceUri = _v;       
        return this;
    },
    
    loadAllSources: function() {
        $.ajax({
           url: BroadSearchWidget.sourceUri,
           data: {type:"all"},
           dataType: 'json',
           success: function(response) {
               BroadSearchWidget.formComponents.treatments.dataSource = response.treatments;
               BroadSearchWidget.formComponents.destinations.dataSource = response.destinations;
           }
        });
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
    
    initializeForm: function(form, components){
        
        BroadSearchWidget.form = form;
        $.extend(true, BroadSearchWidget.formComponents, components);
        
        // load all
        // this may not always be the desirable, especially if ajax request is slow
        BroadSearchWidget.loadAllSources();
        
        $.each(BroadSearchWidget.formComponents, function(type, componentOptions){
            componentOptions.mainInputField.click(function(e){
                componentOptions.dropdownButton.click();
                return false;
            });
            
            componentOptions.dropdownButton.click(function(){
                componentOptions.autocompleteField.autocomplete('search', '');
                componentOptions.autocompleteField.focus();
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
                        });
                        return $('<li>').append(_itemLink).appendTo(_listContainer);
                    })
                };
        });
        
        return this;
    }
}
