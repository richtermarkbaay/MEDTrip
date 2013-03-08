/**
 * @author Allejo Chris G. Velarde
 */
var FrontendStatsTracker = {
  
    clickthroughTrackerUrl: '',
        
    impressionTrackerClass: 'hca_statistics_impressions',
    
    clickthroughTrackerClass: 'hca_statistics_clickthroughs',
    
    statisticParameterAttributeName: 'data-statistic_parameters',
    
    _impressionTrackerForm: '',
    
    _clickThroughTrackerForm: '',
    
    setImpressionTrackerForm: function (_form) {
        
        this._impressionTrackerForm = $(_form);
        
        return this;
    },
    
    setClickthroughTrackerForm: function(_form) {
        this._clickThroughTrackerForm = $(_form);
        return this;
    },
    
    trackClickthroughs: function() {
        $('.'+this.clickthroughTrackerClass).hcaClickthroughTracker();
    },
        
    trackImpressions: function(){
        
        $.each($('.'+FrontendStatsTracker.impressionTrackerClass), function(){
            $('<input type="hidden" name="impressions[]" value="'+$(this).attr(FrontendStatsTracker.statisticParameterAttributeName)+'" />')
                .appendTo(FrontendStatsTracker._impressionTrackerForm);
        });
        
        $.ajax({
           url: FrontendStatsTracker._impressionTrackerForm.attr('action'),
           data: FrontendStatsTracker._impressionTrackerForm.serialize(),
           type: 'post',
           dataType: 'json',
           success: function(){
               
           }
        });
    }
    
};

(function($){
    $.fn.hcaClickthroughTracker = function() {
        $(this).click(function(){
            _link = $(this);
            _inp = FrontendStatsTracker._clickThroughTrackerForm.find('input[type="hidden"][name="clickthroughData"]');
            if (!_inp.length) {
                // no input element, create one
                _inp = $('<input type="hidden" name="clickthroughData" />').appendTo(FrontendStatsTracker._clickThroughTrackerForm);
            }
            _inp.val(_link.attr(FrontendStatsTracker.statisticParameterAttributeName));
            $.ajax({
                url: FrontendStatsTracker._clickThroughTrackerForm.attr('action'),
                data: FrontendStatsTracker._clickThroughTrackerForm.serialize(),
                type: 'POST',
                async: false, // wait for this request before executing other stuff, find a more better way
                dataType: 'json',
                success: function(){},
                error: function(){}
            });
            return true;
        });
        
        return this;
    }
})(jQuery);