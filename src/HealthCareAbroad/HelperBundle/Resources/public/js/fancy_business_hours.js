var FancyBusinessHours = function(_options){
    
    _options = _options || {};
    
    var MINUTES = 60000; // 1 minute in milliseconds
    
    /** helper functions, not part of class **/
    var adjustMeridianTime = function(timepickerTime, dateTime){
        var _parts = {
            hours: timepickerTime.hours,
            minutes: timepickerTime.minutes
        };
        if (timepickerTime.meridian.toLowerCase() == 'pm') {
            _parts.hours = _parts.hours != 12 ? _parts.hours+12 : _parts.hours;
        }
        
        dateTime.setHours(_parts.hours, _parts.minutes);
    };
    
    var generateId = function(){
        // this may return non-unique values. 
        // but given the process in the widget UI, we can assume that this will suffice
        return new Date().getTime();
    };
    
    
    var toTimepickerString = function (dateTime) {
        var meridian = dateTime.getHours() >= 12 ? "PM":"AM";
        var _s = "";
        if (dateTime.getHours() > 12){
            _s = dateTime.getHours()-12+":"+(dateTime.getMinutes()<10?"0"+dateTime.getMinutes():+dateTime.getMinutes())+" "+meridian;
        }
        else {
            _s = dateTime.getHours()+":"+(dateTime.getMinutes()<10?"0"+dateTime.getMinutes():+dateTime.getMinutes())+" "+meridian;
        }
        return _s;
    };
    
    var validateTimeRange = function() {
        var _isValid = FancyBusinessHours.openingTimePickerData.getTime() > FancyBusinessHours.closingTimePickerData.getTime();
        FancyBusinessHours.addButton.attr('disabled', _isValid);
    };
    
    var concatenateDays = function(startDay, endDay) {
        var _label = startDay.day != endDay.day
            ? startDay.short+"-"+endDay.short
            : startDay.short;
        return _label;
    };
    
    var getBitValueOfDay = function(day) {
        var _num = parseInt(day);
        return Math.pow(2, _num);
    };
    /** end helper functions **/
    
    var FancyBusinessHours = {
        rootContainer: null,
        openingTimePicker: null,
        closingTimePicker: null,
        openingTimePickerData: null, // current Date on openingTimePicker widget, will be updated everytime timepicker value is changed
        closingTimePickerData: null, 
        dataContainer: null,
        valueElements: $('#business_hours_value_elements'),
        valuePrototype: null,
        selectorWidget: null,
        addButton: null,
        weekdayCheckboxes: null,
        weekdays: [
           {short: "Sun", long: "Sunday", day: 0}, 
           {short: "Mon", long: "Monday", day: 1},
           {short: "Tue", long: "Tuesday", day: 2},
           {short: "Wed", long: "Wednesday", day: 3},
           {short: "Thu", long: "Thursday", day: 4},
           {short: "Fri", long: "Friday", day: 5},
           {short: "Sat", long: "Saturday", day: 6}
        ],
        data: [], // main data container
        dataPerDay: [] // data collection where in the keys are day, this will be mainly used in validating business hours per day
    };
    
    // set defaults
    FancyBusinessHours.rootContainer = _options.rootContainer || $('#fancy_business_hour_root_container'); 
    FancyBusinessHours.valuePrototype = _options.valuePrototype || null;
    FancyBusinessHours.selectorWidget = _options.selectorWidget || $('#fancy_business_hour_selector_widget');
    FancyBusinessHours.openingTimePicker = _options.openingTimePicker || $('#business_hours_opening');
    FancyBusinessHours.closingTimePicker = _options.closingTimePicker || $('#business_hours_closing');
    FancyBusinessHours.weekdayCheckboxes = _options.weekdayCheckboxes || $('input.business_hours_weekday');
    FancyBusinessHours.addButton = _options.addButton || $('#business_hours_add_button');
    FancyBusinessHours.dataContainer = _options.dataContainer || $('#business_hours_data_container');
    FancyBusinessHours.openingTimePickerData = new Date();
    FancyBusinessHours.openingTimePickerData.setMinutes(0); // reset to 0 minutes
    FancyBusinessHours.closingTimePickerData = new Date(FancyBusinessHours.openingTimePickerData.getTime()+ 60*MINUTES); // set to 1 hour
    
    // initiate data
    for ( var int = 0; int < 7; int++) {
        FancyBusinessHours.dataPerDay[int] = [];
    }
    
    // initialize bootstrap timepicker
    FancyBusinessHours.openingTimePicker.timepicker({defaultTime: toTimepickerString(FancyBusinessHours.openingTimePickerData) })
        .on('changeTime.timepicker', function(e){
            adjustMeridianTime(e.time, FancyBusinessHours.openingTimePickerData);
            validateTimeRange();
        });
    FancyBusinessHours.closingTimePicker.timepicker({defaultTime: toTimepickerString(FancyBusinessHours.closingTimePickerData) })
        .on('changeTime.timepicker', function(e){
            adjustMeridianTime(e.time, FancyBusinessHours.closingTimePickerData);
            validateTimeRange();
        });
    
    // validate the business hour data for selected date
    FancyBusinessHours._isValidBusinessHourForDay = function(day) {
        _valid = true;
        _that = this;
        
        $.each(this.dataPerDay[day], function(){
            // check that the opening time for this data is not in the range of current business hours for this day
            _thatOpeningTime = _that.openingTimePickerData.getTime(); // the current input for opening time
            _thatClosingTime = _that.closingTimePickerData.getTime(); // the current input for closing time
            _thisOpeningTime = this.openingTime; // current loop data.openingTime
            _thisClosingTime = this.closingTime; // current loop data.closingTime
            
            // input for opening time is within range of an existing business hour
            if (_thatOpeningTime >= _thisOpeningTime && _thatOpeningTime <= _thisClosingTime) {
                _valid = false;
                return false; // break
            }
        });
        
        return _valid;
    };
    
    FancyBusinessHours._extractDaysFromWeekdayBitValue = function(bitValue) {
        var days = {};
        for (var x =0;x<this.weekdays.length;x++) {
            var _dayBitValue = getBitValueOfDay(this.weekdays[x].day);
            if (_dayBitValue & bitValue) {
                days[this.weekdays[x].day] = this.weekdays[x].day;
            }
        }
        return days;
    };
    
    FancyBusinessHours._renderItemData = function(_groupedData){
        _groupedWeekdayLabels = [];
        // arrange the days so we can achieve concatenating weekdays 
        _leastDay = null;
        _previousDay = null;
        _that = this;
        $.each(_groupedData.weekdays, function(_index, _day){
            var _currentDay = _that.weekdays[_day];
            if (null == _previousDay) {
                // first loop
                _previousDay = _currentDay;
                _leastDay = _currentDay;
            }
            else {
                // day difference is more than a day
                if ((_currentDay.day - _previousDay.day) > 1) {
                    _groupedWeekdayLabels.push(concatenateDays(_leastDay, _previousDay));
                    _leastDay = _currentDay; // reset marker to current day
                }
                _previousDay = _currentDay;
            }
        });
        
        // add least day since this will not be added in the loop
        if (null != _leastDay) {
            _groupedWeekdayLabels.push(concatenateDays(_leastDay, _previousDay));
        }
        
        this._renderItem({
            label: _groupedWeekdayLabels.join(', '),
            openingDateTime: _groupedData.openingDateTime,
            closingDateTime: _groupedData.closingDateTime,
            elementId: _groupedData.elementId
        });
    };
    
    FancyBusinessHours._renderItem = function(_item) {
        var _html = '<div class="hca-workingday-details">'+
            '<a class="business_hours_remove_link" href="#" data-elementId="'+_item.elementId+'">'+
                '<i class="icon-remove-sign pull-right"></i>'+
            '</a>'+
            '<a class="business_hours_edit_link" href="#" data-elementId="'+_item.elementId+'">'+
                '<i class="icon-edit pull-right"></i>'+
            '</a>'+
            '<span>'+_item.label.toUpperCase()+'</span>'+
            '<b>'+toTimepickerString(_item.openingDateTime)+" - "+toTimepickerString(_item.closingDateTime)+'</b>'
        '</div>';
        
        var _el = $(_html);
        _that = this;
        _el.find('a.business_hours_remove_link').click(function(){
            $(this).parents('div.hca-workingday-details').remove();
            _that.removeData($(this).attr('data-elementId'));
            return false;
        });
        _el.find('a.business_hours_edit_link').click(function(){
            var _icon = $(this).find('i');
            if (_icon.hasClass('icon-edit')) {
                _icon.removeClass('icon-edit').addClass('icon-ok');
                _that.selectorWidget.insertAfter($(this).parents('div.hca-workingday-details'));
            }
            else {
                _icon.removeClass('icon-ok').addClass('icon-edit');
                _that.rootContainer.prepend(_that.selectorWidget); // move to top of root container
            }
            return false;
        });
        this.dataContainer.append(_el);
    };
    
    /**
     * Update the per day data
     */
    FancyBusinessHours._updatePerDayData = function(_groupedData) {
        _that = this;
        $.each(_groupedData.weekdays, function(_key, _data) {
            _that.dataPerDay[_key].push({
                openingTime: _groupedData.openingDateTime.getTime(),
                closingTime: _groupedData.closingDateTime.getTime()
            });
        });
    };
    
    /**
     * Group submitted data by days having same business hours
     */ 
    FancyBusinessHours.groupDaysBySimilarTime = function(days) {
        
        _that = this;
        _groupedData = {
            weekdaysBit: 0,
            weekdays: {},
            openingDateTime: _that.openingTimePickerData,
            closingDateTime: _that.closingTimePickerData
        };
        
        // iterate and validate passed days
        $.each(days, function(){
           var _day = $(this).val();
           
           if (_that._isValidBusinessHourForDay(_day)) {
               _groupedData.weekdaysBit += getBitValueOfDay(_day); // update weekdays bit value of grouped data
               _groupedData.weekdays[_day] = _day; // add this day to the weekdsays grouped data
           }
        });
        
        // has valid weekdays
        if (_groupedData.weekdaysBit > 0) {
            this.addData(_groupedData);
        }
    };
    
    FancyBusinessHours.addData = function(_groupedData){
        _groupedData.elementId = generateId();
        
        this.data.push(_groupedData); // push to main data holder
        this._updatePerDayData(_groupedData); // push to per day data holder
        this._renderItemData(_groupedData); // render the item
        
        // add to form element
        var _newEl = this.valuePrototype.replace(/__name__/g,_groupedData.elementId);
        _newEl = $(_newEl);
        var _valueData = {
            weekdayBitValue: _groupedData.weekdaysBit,
            opening: toTimepickerString(_groupedData.openingDateTime),
            closing: toTimepickerString(_groupedData.closingDateTime)
        };
        
        _newEl.val(window.JSON.stringify(_valueData));
        _newEl.appendTo(this.valueElements);
    };
    
    FancyBusinessHours.removeData = function(elementId){
        
    };
    
    // add button click function
    FancyBusinessHours.addButton.click(function(){
        var _selectedDays = $('input.business_hours_weekday:checked');
        if (_selectedDays.length) {
            var _groupedData = [];
            FancyBusinessHours.groupDaysBySimilarTime(_selectedDays);
        }
        
        return false;
    });
    
    return FancyBusinessHours;
}