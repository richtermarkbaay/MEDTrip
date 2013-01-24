
(function(){
    
    $.fn.truncateByHeight = function(_options){
        _defaultSettings = {
            'visibleRows': 5,
            'scrollable': false
        };
        _options = $.extend(_defaultSettings, _options);
        
        function _shrink(_el) {
            _lineHeight = parseInt(_el.css('line-height'));
            _el.css('height', _options.visibleRows*_lineHeight).css('overflow', 'hidden');
        }
        
        return this.each(function(){
            _el = $(this);
            _lineHeight = parseInt(_el.css('line-height'));
            _height = parseInt(_el.css('height').replace('px'));
            // calculate current number of rows
            _currentNumberOfRows = _height/_lineHeight;
            
            
            if (_currentNumberOfRows > _options.visibleRows) {
                _shrink(_el);
            }
        });
    }
    
})(jQuery);