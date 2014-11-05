var MainSearchWidget = (function() {

    var sourceUri;
    var form;
    var submitButton;

    var selects = {};
    var options = {};

    /**
     * Callers to this function should have already prepared the data (options object),
     * deleting previous values if necessary. Note that this will remove all
     * select options first.
     */
    var populateSelects = function() {
        var populateSelect = function(select, values) {
            select.empty().append($("<option />"));

            $.each(values, function() {
                select.append($("<option />").val(this.value).text(this.label));
            });
        };

        for(var key in options) {
            populateSelect(selects[key], options[key]);
            selects[key].prop('disabled', false).trigger('chosen:updated');
        }
    };

    /**
     * Create the select box objects and attach on change handlers
     */
    var initSelects = function(config) {
        // this should typically be selects, 
        // but we would also need to accommodate hidden fields for narrow search widget
        var specialization = $('[name=' + config.selectSpecialization + ']');
        var subspecialization = $('[name=' + config.selectSubspecialization + ']');
        var treatment = $('[name=' + config.selectTreatment + ']');
        var country = $('[name=' + config.selectCountry + ']');
        var city = $('[name=' + config.selectCity + ']');

        specialization.on('change', function(event, params) {
            specializationOnChange(params);
        });
        subspecialization.on('change', function(params) {
            subspecializationOnChange(params);
        });
        treatment.on('change', function(params) {
            treatmentOnChange(params);
        });
        country.on('change', function(params) {
            countryOnChange(params);
        });
        city.on('change', function(params) {
            cityOnChange(params);
        });

        subspecialization.prop('disabled', true);
        treatment.prop('disabled', true);
        city.prop('disabled', true);

        selects = {
            'specialization': specialization,
            'subspecialization': subspecialization,
            'treatment': treatment,
            'country': country,
            'city': city
        };
    };

    var specializationOnChange = function(params) {
        var searchParams = {};
        searchParams.filter = 'specialization';
        searchParams.specialization = params.selected;

        if (selects.country.val()) {
            searchParams.country = selects.country.val();
            if (selects.city.val()) {
                searchParams.city = selects.city.val();
            }
        }

        var callback = function(response) {
            delete options.specialization;
            options.subspecialization = response.subspecializations;
            options.treatment = response.treatments;

            if (response.countries) {
                options.country = response.countries;
                delete options.city;
            } else {
                delete options.country;
                delete options.city;
            }

            populateSelects();
        };

        retrieveData(searchParams, callback);
    };

    var subspecializationOnChange = function(params) {
        var searchParams = {};
        searchParams.filter = 'subspecialization';
        searchParams.specialization = selects.specialization.val();
        searchParams.subspecialization = selects.subspecialization.val();

        if (selects.country.val()) {
            searchParams.country = selects.country.val();
            if (selects.city.val()) {
                searchParams.city = selects.city.val();
            }
        }

        var callback = function(response) {
            delete options.specialization;
            delete options.subspecialization;
            options.treatment = response.treatments;

            if (response.countries) {
                options.country = response.countries;
                delete options.city;
            } else {
                delete options.country;
                delete options.city;
            }

            populateSelects();
        };

        retrieveData(searchParams, callback);

    };

    var treatmentOnChange = function(params) {
        var searchParams = {};
        searchParams.filter = 'treatment';
        searchParams.specialization = selects.specialization.val();

        if (selects.subspecialization.val()) {
            searchParams.subspecialization = selects.subspecialization.val();
        }

        searchParams.treatment = selects.treatment.val();;

        if (selects.country.val()) {
            searchParams.country = selects.country.val();
            if (selects.city.val()) {
                //searchParams.city = selects.city.val();
                // every option has been selected so we exit
                return;
            }
        }

        var callback = function(response) {
            delete options.specialization;
            delete options.subspecialization;
            delete options.treatment;

            if (response.countries) {
                options.country = response.countries;
                delete options.city;
            } else {
                // why should this not be reachable?
                // this gets executed when treatment is changed while a country is already selected
                // returning a list of cities
                console.log('This should be unreachable code');
            }

            populateSelects();
        };

        retrieveData(searchParams, callback);
    };

    var countryOnChange = function(params) {
        var searchParams = {};
        searchParams.filter = 'country';
        searchParams.country = selects.country.val();

        if (selects.specialization.val()) {
            searchParams.specialization = selects.specialization.val();
        }
        if (selects.subspecialization.val()) {
            searchParams.subspecialization = selects.subspecialization.val();
        }
        if (selects.treatment.val()) {
            searchParams.treatment = selects.treatment.val();
        }

        var callback = function(response) {
            if (response.specializations) {
                options.specialization = response.specializations;
            } else {
                delete options.specialization;
            }
            if (response.subspecializations) {
                options.subspecialization = response.subspecializations;
            } else {
                delete options.subspecialization;
            }
            if (response.treatments) {
                options.treatment = response.treatments;
            } else {
                delete options.treatment;
            }
            delete options.country;
            options.city = response.cities;

            populateSelects();
        };

        retrieveData(searchParams, callback);
    };

    var cityOnChange = function(event, params) {
        if (selects.specialization.val() && selects.subspecialization.val() && selects.treatment.val()) {
            //nothing to do
            return;
        }

        var searchParams = {};
        searchParams.filter = 'city';
        searchParams.country = selects.country.val();

        if (selects.specialization.val()) {
            searchParams.specialization = selects.specialization.val();
        }
        if (selects.subspecialization.val()) {
            searchParams.subspecialization = selects.subspecialization.val();
        }
        if (selects.treatment.val()) {
            searchParams.treatment = selects.treatment.val();
        }

        var callback = function(response) {
            if (response.specializations) {
                options.specialization = response.specializations;
            } else {
                delete options.specialization;
            }
            if (response.subspecializations) {
                options.subspecialization = response.subspecializations;
            } else {
                delete options.subspecialization;
            }
            if (response.treatments) {
                options.treatment = response.treatments;
            } else {
                delete options.treatment;
            }

            delete options.country;
            delete options.city;

            populateSelects();
        };

        retrieveData(searchParams, callback);
    };

    var retrieveData = function(params, responseHandler) {
        $.ajax({
            url: sourceUri,
            data: params,
            dataType: 'json',
            success: function(response) {
                responseHandler(response);
            }
        });
    };

    ////////////////////////////////////////////////////////////////////////////
    // Public API
    ////////////////////////////////////////////////////////////////////////////

    var init = function(config) {
        $(config.chosen.selector).chosen(config.chosen.options);

        sourceUri = config.sourceUri;
        
        var callback = function(response){
            if(response.specializations != undefined)
                options['specialization'] = response.specializations;
            if(response.subspecializations != undefined)
                options['subspecialization'] = response.subspecializations;
            if(response.treatments != undefined)
                options['treatment'] = response.treatments;
            if(response.countries != undefined)
                options['country'] = response.countries;
            if(response.cities != undefined)
                options['city'] = response.cities;
            
            initSelects(config);
            populateSelects();
        }

        if (typeof(config.preloadedCountries) != 'undefined' && typeof(config.preloadedSpecializations) != 'undefined') {
            callback({countries:config.preloadedCountries,specializations:config.preloadedSpecializations});
        } 
        else {
            retrieveData(config.preloadingParams,callback);
        }

        submitButton = $('[name=' + config.submitButton + ']');
        submitButton.on('click', function(event, params) {
            var isInvalidForm = (function() {
                var noOptionSelected = true;
                for (var select in selects) {
                    if (selects[select].val()) {
                        noOptionSelected = false;
                        break;
                    }
                }
                return noOptionSelected;
            })();

            if (isInvalidForm) {
                return;
            }

            document.forms[config.form].submit();
        });
    };

    return {
        init: init
    };
})();
