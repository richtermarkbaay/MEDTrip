/**
 * @author Allejo Chris G. Velarde
 */
var FrontendStatsTracker = {
  
    impressionTrackerClass: 'hca_statistics_impressions',
    
    clickthroughTrackerClass: 'hca_statistics_clickthroughs',
    
    statisticParameterAttributeName: 'data-statistic_parameters',
    
    _impressionTrackerForm: '',
    
    setImpressionTrackerForm: function (_form) {
        
        this._impressionTrackerForm = $(_form);
        
        return this;
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