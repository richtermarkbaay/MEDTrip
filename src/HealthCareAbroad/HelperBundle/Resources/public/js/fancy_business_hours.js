var FancyBusinessHours = function(_options){
    _options = _options || {};
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
        sortedData: []
    };
    
    // 1 minute in milliseconds
    var MINUTES = 60000;
    
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
        FancyBusinessHours.data[int] = [];
    }
    
    var adjustMeridianTime = function(timepickerTime, dateTime){
        var _parts = {
            hours: timepickerTime.hours,
            minutes: timepickerTime.minutes
        };
        if (timepickerTime.meridian.toLowerCase() == 'pm') {
            _parts.hours = _parts.hours+12;
        }
        
        dateTime.setHours(_parts.hours, _parts.minutes);
    };
    
    
    var toTimepickerString = function (dateTime) {
        var meridian = dateTime.getHours() > 12 ? "PM":"AM";
        var _s = "";
        if (dateTime.getHours() > 12){
            _s = dateTime.getHours()-12+":"+(dateTime.getMinutes()<10?"0"+dateTime.getMinutes():+dateTime.getMinutes())+" PM";
        }
        else {
            _s = dateTime.getHours()+":"+(dateTime.getMinutes()<10?"0"+dateTime.getMinutes():+dateTime.getMinutes())+" AM";
        }
        return _s;
    };
    
    var validateTimeRange = function() {
        var _isValid = FancyBusinessHours.openingTimePickerData.getTime() > FancyBusinessHours.closingTimePickerData.getTime();
        FancyBusinessHours.addButton.attr('disabled', _isValid);
    };
    
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
    FancyBusinessHours.validateData = function(data) {
        _valid = true;
        _that = this;
        $.each(this.data[data.day], function(){
            // check that the opening time for this data is not in the range of current business hours for this day
            _thatOpeningTime = _that.openingTimePickerData.getTime(); // the current input for opening time
            _thatClosingTime = _that.closingTimePickerData.getTime(); // the current input for closing time
            _thisOpeningTime = this.openingDateTime.getTime(); // current loop data.openingTime
            _thisClosingTime = this.closingDateTime.getTime(); // current loop data.closingTime
            
            // input for opening time is within range of an existing business hour
            if (_thatOpeningTime >= _thisOpeningTime || _thatOpeningTime <= _thisClosingTime) {
                _valid = false;
                return false; // break
            }
        });
        console.log(_valid);
        
        return true;
    };
    
    FancyBusinessHours.addData = function(data){
        
        // validate data first
        if (!this.validateData(data)) {
            return;
        }
        
        // draw this to the data container
        var _cls = 'business_hours_data_day_'+data.day;
        var _html = "<tr class='data_row "+_cls+"'>" +
        		"<td>"+FancyBusinessHours.weekdays[data.day].long+"</td>" +
				"<td>"+data.opening+" - "+data.closing+"</td>" +
				"<td><a href='#' class='business_hours_remove_link'><i class='icon-remove-sign'></i></a></td>" +
    		"</tr>";
        var _item = $(_html);
        _item.find('a.business_hours_remove_link').click(function(){
            $(this).parents('tr.data_row').remove();
            return false;
        });
        // find an existing churva
        this.dataContainer.append(_item);
        
        this.data[data.day].push({openingDateTime: this.openingTimePickerData, closingDateTime: this.closingTimePickerData});
        console.log(this.data[data.day].length);
    };
    
    // add button click function
    FancyBusinessHours.addButton.click(function(){
        var _selectedDays = $('input.business_hours_weekday:checked');
        if (_selectedDays.length) {
            var _groupedData = [];
            $.each(_selectedDays, function(){
                _this = $(this);
                var _data = {
                    day: _this.val(),
                    opening: FancyBusinessHours.openingTimePicker.val(),
                    closing: FancyBusinessHours.closingTimePicker.val()
                };
                FancyBusinessHours.addData(_data);
            });
            
        }
        
        return false;
    });
    
    return FancyBusinessHours;
}