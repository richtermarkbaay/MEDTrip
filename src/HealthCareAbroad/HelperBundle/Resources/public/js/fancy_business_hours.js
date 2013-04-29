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
        openingTimePicker: null,
        closingTimePicker: null,
        openingTimePickerData: null, // current Date on openingTimePicker widget, will be updated everytime timepicker value is changed
        closingTimePickerData: null, 
        dataContainer: null,
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
        data: [],
        dataPerDay: [] // data collection where in the keys are day
    };
    
    // set defaults
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
    
    // group submitted data by days having same business hours
    FancyBusinessHours.groupDaysBySimilarTime = function(days) {
        
        _that = this;
        _groupedData = {
            weekdaysBit: 0,
            weekdays: [],
            openingDateTime: _that.openingTimePickerData,
            closingDateTime: _that.closingTimePickerData
        };
        
        $.each(days, function(){
           var _day = $(this).val(); 
           var _data = {
               day: _day,
               openingTime: _that.openingTimePickerData.getTime(),
               closingTime: _that.closingTimePickerData.getTime(),
               bitValue: getBitValueOfDay(_day),
               opening: toTimepickerString(_that.openingTimePickerData),
               closing: toTimepickerString(_that.closingTimePickerData)
           };
           
           if (_that._isValidBusinessHourForDay(_day)) {
               _that.dataPerDay[_day].push(_data); // store in per day data
               _groupedData.weekdaysBit += _data.bitValue; // update weekdays bit value of grouped data
               _groupedData.weekdays.push(_data.day); // add this day to the weekdsays grouped data
           }
        });
        
        // has valid weekdays
        if (_groupedData.weekdaysBit > 0) {
            _that.data.push(_groupedData);
            this._renderItem(_groupedData); // render the item
        }
    };
    
    FancyBusinessHours._renderItem = function(_groupedData){
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
        
        this._renderItemData({
            label: _groupedWeekdayLabels.join(', '),
            openingDateTime: _groupedData.openingDateTime,
            closingDateTime: _groupedData.closingDateTime
        });
    };
    
    FancyBusinessHours._renderItemData = function(_item) {
        var _cls = 'business_hours_data_day_';
        var _html = "<tr class='data_row "+_cls+"'>" +
                "<td>"+_item.label+"</td>" +
                "<td>"+toTimepickerString(_item.openingDateTime)+" - "+toTimepickerString(_item.closingDateTime)+"</td>" +
                "<td><a href='#' class='business_hours_remove_link'><i class='icon-remove-sign'></i></a></td>" +
            "</tr>";
        var _el = $(_html);
        _el.find('a.business_hours_remove_link').click(function(){
            $(this).parents('tr.data_row').remove();
            return false;
        });
        this.dataContainer.append(_el);
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