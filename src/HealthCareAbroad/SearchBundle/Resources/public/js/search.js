var NarrowSearchWidgetManager = {
        
    _defaultWidgetOptions: {
        'widget_container': '',
        'autocomplete': {
            'field': 'input.search-field',
            'selected_value_container': '.autocomplete_selected_value_container',
            'autocomplete_list_container': '.autocomplete_list_container',
            'source': ''
        }
    },
        
    initializeWidgets: function(_widgets) {
        $.each(_widgets, function(_k, _v){
            var _widgetOptions = $.extend(NarrowSearchWidgetManager._defaultWidgetOptions, _v);
            NarrowSearchWidgetManager._autocomplete($(_v.widget_container).find(_v.autocomplete.field), _v);
//            $(_v.widget_container).find(_v.autocomplete.field)
//                .narrowSearchAutocomplete({source: _widgetOptions.autocomplete.source});
            
        });
        
        return this;
    },
    
    _autocomplete: function(_widget, _options) {
        var _xhr;
        var _self = $(_widget);
        
        _self
            .click(function(e){
                e.stopPropagation();
            })
            .autocomplete({
            minLength: 0,
            source: function(request, response) {
                if (_xhr && 4 != _xhr.readyState) {
                    _xhr.abort();
                }
                _xhr = $.ajax({
                    url: _options.autocomplete.source,
                    type: 'post',
                    data: { term: request.term, documentType: 'test' },
                    dataType: 'json',
                    success: function(json) {
                        response($.each(json, function(index, item) {
                            return { label: item.label, value: item.id }
                        }));
                    }
                });
            },
            focus: function() {
                // prevent value inserted on focus
                return false;
            }
        }).data('autocomplete')._renderMenu = function(ul, data){
            ul.remove();
            var _listContainer = $(this.element.attr('data-listContainer'));
            _listContainer.html('');
            $.each(data, function(index, item){
                var _itemLink = $('<a data-value="'+item.value+'">'+item.label+'</a>')
                    .bind('click', function(){
                        // replace value of selected value container
                        $(_options.widget_container).find(_options.autocomplete.selected_value_container).html($(this).html());
                    });
                
                return $('<li>').append(_itemLink)
                    .appendTo(_listContainer);
            })
        };
        
        return this;
    }
};

//(function($){
//    $.fn.narrowSearchAutocomplete = function(_options){
//        _options = $.extend({source: ''}, _options);
//        
//
//        return this;  
//    };
//})(jQuery);

