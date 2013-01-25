/**
 * @author Chaztine Blance
 */
var Institution = {
		
	addAncillaryService: function(_linkElement) {
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    removeAncillaryService: function(_linkElement) {
    	
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    _doAncillaryServiceAction: function (_linkElement) {
   
    
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html('Processing...').addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                _linkElement.parents('tr.ancillaryancillaryServices_row').html($(response.html).html());
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    }
};
        