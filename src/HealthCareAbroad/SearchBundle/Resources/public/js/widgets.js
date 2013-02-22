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
            'autocompleteSearchParams': {'id': '', 'label':''}
        },
        destinations: {
            'mainInputField': null,
            'dropdown': null,
            'dropdownButton': null,
            'autocompleteField': null,
            'dataSource': {},
            'autocompleteSearchParams': {'id': '', 'label':''}
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
    
    loadSourcesByType: function(type, params) {
        if (!(type != 'treatments' || type != 'destinations')) {
            $.ajax({
                url: BroadSearchWidget.sourceUri,
                data: {type:type},
                dataType: 'json',
                success: function(response) {
                    BroadSearchWidget.formComponents['type'].dataSource = response['type'];
                }
             });
        }
    },
    
    initializeForm: function(form, components){
        
        // load all
        BroadSearchWidget.loadAllSources();
        
        BroadSearchWidget.form = form;
        $.extend(BroadSearchWidget.formComponents, components);
        
        $.each(BroadSearchWidget.formComponents, function(type, componentOptions){
            componentOptions.mainInputField.click(function(e){
                componentOptions.dropdownButton.click();
                return false;
            });
            
            componentOptions.dropdownButton.click(function(){
                componentOptions.autocompleteField.autocomplete('search', '');
                componentOptions.autocompleteField.focus();
                console.log($(this).attr('id'));
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
                            //BroadSearchWidget
                        });
                        return $('<li>').append(_itemLink).appendTo(_listContainer);
                    })
                };
        });
        
        return this;
    }
}
