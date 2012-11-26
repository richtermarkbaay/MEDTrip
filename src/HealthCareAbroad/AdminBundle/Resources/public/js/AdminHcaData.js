var AdminTreatmentsManager = {
        
    options: {
        loadSubSpecializationsBySpecializationUrl: '',
        subSpecializationsDropdownElement: null
    },
    
    init: function(_options) {
        $.extend(AdminTreatmentsManager.options, _options);
    },
        
    loadSubSpecializationsBySpecialization: function(specializationId, additionalData) {
        
        _data = {
            id: specializationId,
            empty_value: 'All'
        };
        _data = $.extend(_data, additionalData); 
        
        AdminTreatmentsManager.options.subSpecializationsDropdownElement.attr('disabled', true).html('<option value="0">Loading...</option>');
        $.ajax({
            url: AdminTreatmentsManager.options.loadSubSpecializationsBySpecializationUrl,
            data: _data,
            type: 'get',
            dataType: 'json',
            success: function(json){
                if (json.data.length > 0) {
                    AdminTreatmentsManager.options.subSpecializationsDropdownElement.attr('disabled', false).html(json.html);
                } 
                else {
                    AdminTreatmentsManager.options.subSpecializationsDropdownElement.attr('disabled', true).html('<option value="0">No sub specializations</option>');
                }
                
            }
        });
    }
}