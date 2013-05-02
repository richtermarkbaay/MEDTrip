/** global functions/values that are used in fbh components **/
var MINUTES = 60000; // 1 minute in milliseconds

var WEEKDAYS = [
   {short: "Sun", long: "Sunday", day: 0}, 
   {short: "Mon", long: "Monday", day: 1},
   {short: "Tue", long: "Tuesday", day: 2},
   {short: "Wed", long: "Wednesday", day: 3},
   {short: "Thu", long: "Thursday", day: 4},
   {short: "Fri", long: "Friday", day: 5},
   {short: "Sat", long: "Saturday", day: 6}
];

var generateId = function(){
    // this may return non-unique values. 
    // but given the process in the widget UI, we can assume that this will suffice
    return new Date().getTime();
};

var getBitValueOfDay = function(day) {
    var _num = parseInt(day);
    return Math.pow(2, _num);
};

//convert Date object to string recognized by timepicker widget
var toTimepickerString = function(dateTime){
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

/** end global functions/values that are used in fbh components **/

/**
 * FancyBusinessHours class component
 */
var FancyBusinessHours = function(_options){
  
    var FancyBusinessHours = {
        selectorWidgetPrototype: '',
        selectedHourPrototype: '',
        formValuePrototype: '',
        rootContainer: null,        
        data: {}, // main data
        dataPerDay: [], // data with days as keys. used for validating time
        dataContainer: null,
        formValueContainer: null,
        _addWidget: null // 
    };
    
    // set default properties
    FancyBusinessHours.selectorWidgetPrototype = _options.selectorWidgetPrototype;
    FancyBusinessHours.rootContainer = _options.rootContainer;
    FancyBusinessHours.selectedHourPrototype = _options.selectedHourPrototype;
    FancyBusinessHours.formValuePrototype = _options.formValuePrototype;
    FancyBusinessHours.dataContainer = _options.dataContainer ||  $('#fbh_data_container');
    FancyBusinessHours.formValueContainer = _options.formValueContainer || $('#fbh_value_elements_container');
    
    /**
     * Initialize the component
     */
    FancyBusinessHours.initialize = function(){
        
        // initialize per day data
        for (var _x=0;_x<WEEKDAYS.length;_x++){
            this.dataPerDay[WEEKDAYS[_x].day] = [];
        }
        
        // we always create the new selector widget for adding business hours
        this._renderInitialWidget();
        
        return this;
    };
    
    /**
     * Add to main data. Returns the added data object if data is valid
     * 
     * @param array days
     * @param Date openingDateTime
     * @param Date closingDateTime
     * 
     * @return object
     */
    FancyBusinessHours.addData = function(days, openingDateTime, closingDateTime){
        // validate parameters
        if (!(openingDateTime instanceof Date && closingDateTime instanceof Date) ) {
            // invalid parameters
            return null;
        }
        _that = this;
        _groupedData = {
            id: generateId(), // identifier for this data, this will be used in data removal operations
            weekdaysBit: 0,
            weekdays: {},
            openingDateTime: openingDateTime,
            closingDateTime: closingDateTime,
            formElement: null
        };
        
        // iterate and validate data for each passed day
        $.each(days, function(_key, _day){
            if (_that._isValidBusinessHourForDay(_day, openingDateTime, closingDateTime)) {
                _groupedData.weekdays[_day] = _day;
                _groupedData.weekdaysBit += getBitValueOfDay(_day);
            }
        });
        
        if (_groupedData.weekdaysBit > 0) {
            this.data[_groupedData.id] = _groupedData;
            this._updatePerDayData(_groupedData);
            this._renderItemData(_groupedData);
            _groupedData.formElement = this._addFormValue(_groupedData);
            return _groupedData;
        }
        else {
            // passed data has no valid value
            return null;
        }
    };
    
    /**
     * Remove a data by identifier
     * 
     * @param string dataId
     */
    FancyBusinessHours.removeData = function(dataId) {
        if (this.data[dataId]) {
            
            // remove per day data
            $.each(this.dataPerDay, function(_key, _data){
                var _temp = _data;
                for (var _x=0; _x<_data.length; _x++) {
                    if (_data[_x].id == dataId) {
                        _data.splice(_x, 1);
                    }
                }
                
            });
            
            // remove form element
            if (this.data[dataId]['formElement']) {
                this.data[dataId]['formElement'].remove();
            }
            
            // remove from main data
            delete this.data[dataId];
        }
    };
    
    /** internal functions **/
    FancyBusinessHours._renderInitialWidget = function(){
        this._addWidget = new FancyBusinessHourWidget(this);
        this._addWidget.initialize();
        this.rootContainer.prepend(this._addWidget.element);
    };
    
    // validate a passed business hour for a certain day
    FancyBusinessHours._isValidBusinessHourForDay = function(day, openingDateTime, closingDateTime) {
        _valid = true;
        _that = this;
       
        // iterate thru current per day data to validate conflicting time
        $.each(_that.dataPerDay[day], function(){
            _inputOpeningTimestamp = openingDateTime.getTime();
            _inputClosingTimestamp = closingDateTime.getTime();         
            // input for opening time is within range of an existing business hour
            if (_inputOpeningTimestamp >= this.openingTimestamp && _inputClosingTimestamp <= this.closingTimestamp) {
                _valid = false;
                return false; // break loop
            }
        });
        
        return _valid;
    };
    
    // update the dataPerDay content
    FancyBusinessHours._updatePerDayData = function(_groupedData) {
        _that = this;
        $.each(_groupedData.weekdays, function(_key, _data){
            _that.dataPerDay[_key].push({
                id: _groupedData.id,
                openingTimestamp: _groupedData.openingDateTime.getTime(),
                closingTimestamp: _groupedData.closingDateTime.getTime()
            });
        });
    };
    
    /**
     * Add the data as a form value
     * 
     * @return object the jQuery object for the added element
     */
    FancyBusinessHours._addFormValue = function(data) {
        // add to form element
        var _newEl = this.formValuePrototype.replace(/__name__/g,data.id);
        _newEl = $(_newEl);
        var _valueData = {
            weekdayBitValue: data.weekdaysBit,
            opening: toTimepickerString(data.openingDateTime),
            closing: toTimepickerString(data.closingDateTime)
        };
        _newEl.val(window.JSON.stringify(_valueData));
        _newEl.appendTo(this.formValueContainer);
        
        return _newEl;
    };
    
    // render data in value container
    FancyBusinessHours._renderItemData = function(data) {
        _groupedWeekdayLabels = [];
        // arrange the days so we can achieve concatenating weekdays 
        _leastDay = null;
        _previousDay = null;
        _that = this;
        $.each(data.weekdays, function(_index, _day){
            var _currentDay = WEEKDAYS[_day];
            if (null == _previousDay) {
                // first loop
                _previousDay = _currentDay;
                _leastDay = _currentDay;
            }
            else {
                // day difference is more than a day
                if ((_currentDay.day - _previousDay.day) > 1) {
                    _groupedWeekdayLabels.push(_concatenateDays(_leastDay, _previousDay));
                    _leastDay = _currentDay; // reset marker to current day
                }
                _previousDay = _currentDay;
            }
        });
        
        // add least day since this will not be added in the loop
        if (null != _leastDay) {
            _groupedWeekdayLabels.push(_concatenateDays(_leastDay, _previousDay));
        }
        
        // render the selected item
        this._renderItem({
            label: _groupedWeekdayLabels.join(', '),
            openingDateTime: data.openingDateTime,
            closingDateTime: data.closingDateTime,
            elementId: data.id
        });
        
    }; // end _renderItemData
    
    // render item
    FancyBusinessHours._renderItem = function(item) {
        _that = this;
        var _el = $(this.selectedHourPrototype);
        var _removeLink = _el.find('a.fbh_remove_selected_item_link');
        var _editLink = _el.find('a.fbh_edit_selected_item_link');
        
        _el.find('.fbh_selected_days_label').html(item.label.toUpperCase());
        _el.find('.fbh_selected_time_label').html(toTimepickerString(item.openingDateTime)+" - "+toTimepickerString(item.closingDateTime));
        
        _removeLink.attr('data-elementId', item.elementId)
            .click(function(){
                $(this).parents('.fbh_selected_item').remove();
                _that.removeData(item.elementId);
                
                return false;
            });
        
        _editLink.attr('data-elementId', item.elementId)
            .click(function(){
                return false;
            });
        
        _that.dataContainer.append(_el);
    }; // end _renderItem
    
    FancyBusinessHours._extractDaysFromWeekdayBitValue = function(bitValue) {
        var days = {};
        for (var x =0;x<WEEKDAYS.length;x++) {
            var _dayBitValue = getBitValueOfDay(WEEKDAYS[x].day);
            if (_dayBitValue & bitValue) {
                days[WEEKDAYS[x].day] = WEEKDAYS[x].day;
            }
        }
        return days;
    };
    
    /** end internal functions **/
    
    /** private  functions **/
    var _concatenateDays = function(startDay, endDay) {
        var _label = startDay.day != endDay.day
            ? startDay.short+"-"+endDay.short
            : startDay.short;
        return _label;
    };
    /** end private  functions **/
    
    return FancyBusinessHours;
    
};// end FancyBusinessHours

/**
 * Selector widget class
 */
var FancyBusinessHourWidget = function(owner){
    
    var FancyBusinessHourWidget = {
        _owner: owner, // FancyBusinessHours instance
        openingTimePicker: null, // timepicker UI widget
        openingTimePickerData: null, // Date object
        closingTimePicker: null, // timepicker UI widget
        closingTimePickerData: null, // Date object
        element: null // element that represents this whole widget
    };
    
    FancyBusinessHourWidget._owner = owner; // owner of this widget
    
    /**
     * Initialize selector widget
     */
    FancyBusinessHourWidget.initialize = function(){
        // get the prototype
        this.element = $(this._owner.selectorWidgetPrototype);
        
        // init the timepicker elements
        this.openingTimePicker = this.element.find('.fbh_timepicker_opening');
        this.openingTimePickerData = new Date();
        this.openingTimePickerData.setMinutes(0); // reset minutes to 0
        this.openingTimePickerData.setSeconds(0); // reset seconds to 0
        this.openingTimePickerData.setMilliseconds(0); // reset milliseconds to 0
        this.closingTimePicker = this.element.find('.fbh_timepicker_closing');
        this.closingTimePickerData = new Date(this.openingTimePickerData.getTime()+(60*MINUTES)); // set to one hour from opening time
        this._initializeTimepickerWidget(this.openingTimePicker, this.openingTimePickerData); // init the opening time widget
        this._initializeTimepickerWidget(this.closingTimePicker, this.closingTimePickerData); // init the closing time widget
        // -- end timepicker initialization
        
        // initialize on click of add button
        _thatWidgetInstance = this;
        this.element.find('.fbh_add_button').click(function(){
            // call
            var _checkboxes = _thatWidgetInstance.element.find('input.fbh_weekdays:checked');
            if (_checkboxes.length) {
                var _data = {
                    days: {},
                    openingDateTime: null,
                    closingDateTime: null
                };
                // get the selected days
                for (var _x=0;_x<_checkboxes.length;_x++) {
                    var _val = parseInt(_checkboxes[_x].value);
                    _data.days[_val] = _val; 
                }
                // set the opening and closing time
                _data.openingDateTime = _thatWidgetInstance.openingTimePickerData;
                _data.closingDateTime = _thatWidgetInstance.closingTimePickerData;
                
                // push data to main component data
                _thatWidgetInstance._owner.addData(_data.days, _data.openingDateTime, _data.closingDateTime);
            }
            
            return false;
        });
        // -- end button click handler
        
        return this;
    }; // -- end of initialize
    
    
    
    /** internal helper functions **/
    // initialize timepicker widget
    FancyBusinessHourWidget._initializeTimepickerWidget = function(widget, datetime){
        widget.timepicker({defaultTime: toTimepickerString(datetime) })
            .on('changeTime.timepicker', function(e){
                _adjustMeridianTime(e.time, datetime);
            });
    };
    /** end internal helper functions **/
    
    // adjust hour part of datetime object based on timepicker input
    var _adjustMeridianTime = function(timepickerTime, dateTime) {
        var _parts = {
            hours: timepickerTime.hours,
            minutes: timepickerTime.minutes
        };
        if (timepickerTime.meridian.toLowerCase() == 'pm') {
            _parts.hours = _parts.hours != 12 ? _parts.hours+12 : _parts.hours;
        }
        
        dateTime.setHours(_parts.hours, _parts.minutes);
    };
    
    // return definition of FancyBusinessHourWidget so this can be instantiated by the client
    return FancyBusinessHourWidget;
};// end FancyBusinessHourWidget