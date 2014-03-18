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
    // http://stackoverflow.com/questions/105034/how-to-create-a-guid-uuid-in-javascript
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
    // this may return non-unique values. 
    // but given the process in the widget UI, we can assume that this will suffice
    //return new Date().getTime();
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
        _addWidget: null, //
        _createdWidgets: {}
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
    FancyBusinessHours.addData = function(days, openingDateTime, closingDateTime, notes){
        var _that = this;
        var _groupedData = this._processData({
            id: generateId(),
            weekdays: days,
            openingDateTime: openingDateTime,
            closingDateTime: closingDateTime,
            notes: notes
        });
        
        if (_groupedData && _groupedData.weekdaysBit > 0) {
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
    
    FancyBusinessHours.updateData = function(dataId, newData) {
        if (this.data[dataId]) {
            newData.id = dataId;
            var oldData = this.data[dataId];
            
            // we remove current data so we can validate thru other data
            this.removeData(dataId); // remove existing data to avoid conflict
            
            // process new data
            var _processedData = this._processData(newData);
            
            if (!(_processedData && _processedData.weekdaysBit > 0)) {
                // new data is not valid, we revert to oldData which is a reference to the old data
                _processedData = oldData
            }
            
            this.data[dataId] = _processedData;
            this._updatePerDayData(_processedData);
            _processedData.formElement = this._addFormValue(_processedData);
            
            // update the view part for this selected item
            this._renderItemData(this.data[dataId]);
        }
        return null;
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
    
    /**
     * Create a new selector widget
     * 
     * @param string widget uuid
     * @param object initial data that will be set to the widget
     */
    FancyBusinessHours.createNewWidget = function(widgetId, _initialData){
        var _data =  _initialData || {};
        var _w = new FancyBusinessHourWidget(this);
        _w.uuid = widgetId;
        _w.initialize(_data.weekdays || null, _data.openingDateTime || null, _data.closingDateTime || null, _data.notes || null);
        this._createdWidgets[_w.uuid] = _w;
        return _w;
    };
    
    /**
     * Get a widget
     * 
     * @param string widget uuid
     */
    FancyBusinessHours.getWidget = function(widgetId){
        return this._createdWidgets[widgetId] 
            ? this._createdWidgets[widgetId]
            : null;
    };
    
    FancyBusinessHours.removeWidget = function(widgetId){
        if (this._createdWidgets[widgetId]) {
            this._createdWidgets[widgetId].element.remove();
            delete this._createdWidgets[widgetId]; 
        }
    };
    
    FancyBusinessHours.showWidget = function(widgetId){
        if (this._createdWidgets[widgetId]) {
            this._createdWidgets[widgetId].element.slideDown();
        }
        else {
            console.log('No widget '+widgetId);
        }
    };
    
    FancyBusinessHours.hideWidget = function(widgetId){
        if (this._createdWidgets[widgetId]) {
            this._createdWidgets[widgetId].element.slideUp();
        }
        else {
            console.log('No widget '+widgetId);
        }
    };
    
    /** internal functions **/
    // process raw data
    FancyBusinessHours._processData = function(_data) {
        var _raw = _data || {
            id: null,
            weekdays: {},
            openingDateTime: null,
            closingDateTime: null,
            notes: ''
        };
        // check required data
        if (_raw.id && _raw.openingDateTime && _raw.openingDateTime instanceof Date && _raw.closingDateTime && _raw.closingDateTime instanceof Date){
            var _processedData = $.extend({}, _raw); // _processedData will be the processed data
            var _that = this;
            
            _processedData.weekdaysBit = 0; // reset weekdaysBit value
            _processedData.weekdays = {}; // reset weekdays
            _processedData.formElement = _raw.formElement || null; // init form element
            
            // iterate and validate data for each passed day
            $.each(_raw.weekdays, function(_key, _day){
                if (_that._isValidBusinessHourForDay(_day, _processedData.openingDateTime, _processedData.closingDateTime)) {
                    _processedData.weekdays[_day] = _day;
                    _processedData.weekdaysBit += getBitValueOfDay(_day);
                }
                else {
                    HCA.alertMessage('error', 'We have detected a conflict in the schedule you have entered. Please recheck your schedule.');
                	console.log(WEEKDAYS[_day].long + ' '+toTimepickerString(_processedData.openingDateTime) + ' '+toTimepickerString(_processedData.closingDateTime)+' is invalid');
                }
            });
            return _processedData;
        }
        return null;
    };
    
    // capture input data from a specific widget
    FancyBusinessHours._captureDataFromWidget = function(_widget){
        if (_widget ) {
            var _checkboxes = _widget.element.find('input.fbh_weekdays:checked');
            if (_checkboxes.length) {
                var _data = {
                    weekdays: {},
                    openingDateTime: null,
                    closingDateTime: null,
                    notes: ''
                };
                // get the selected days
                for (var _x=0;_x<_checkboxes.length;_x++) {
                    var _val = parseInt(_checkboxes[_x].value);
                    _data.weekdays[_val] = _val; 
                }
                // set the opening and closing time
                _data.openingDateTime = _widget.openingTimePickerData;
                _data.closingDateTime = _widget.closingTimePickerData;
                _data.notes = $.trim(_widget.notesElement.val());
                return _data;
            }
        }
        return null;
    };
    
    FancyBusinessHours._renderInitialWidget = function(){
        this._addWidget = this.createNewWidget(generateId());
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
            if (_inputOpeningTimestamp >= this.openingTimestamp && _inputClosingTimestamp <= this.closingTimestamp ) {
                _valid = false;
                return false; // break loop
            }
            else if(_inputOpeningTimestamp >= this.openingTimestamp && _inputClosingTimestamp >= this.closingTimestamp ) {
                _valid = false;
                return false; // break loop
            }
            else if(_inputOpeningTimestamp <= this.openingTimestamp && _inputClosingTimestamp >= this.closingTimestamp ) {
                _valid = false;
                return false; // break loop
            }
            else if(_inputOpeningTimestamp <= this.openingTimestamp && _inputClosingTimestamp <= this.closingTimestamp ) {
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
            closing: toTimepickerString(data.closingDateTime),
            notes: data.notes
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
            weekdays: data.weekdays,
            openingDateTime: data.openingDateTime,
            closingDateTime: data.closingDateTime,
            elementId: data.id,
            notes: data.notes
        });
        
    }; // end _renderItemData
    
    // render item
    FancyBusinessHours._renderItem = function(item) {
        
        var _el = this.dataContainer.find('.fbh_selected_item[data-elementId='+item.elementId+']');
        if (_el.length) {
            // there is an existing item, update it
            _el.find('.fbh_selected_days_label').html(item.label.toUpperCase());
            _el.find('.fbh_selected_time_label').html(toTimepickerString(item.openingDateTime)+" - "+toTimepickerString(item.closingDateTime));
            
            // update the notes
            _toggleNotes(_el.find('.fbh_selected_item_notes'),item.notes);
        }
        else {
            this._renderNewItem(item);
        }
        
    }; // end _renderItem
    
    // render a new selected item
    FancyBusinessHours._renderNewItem = function (item) {
        
        var _el = $(this.selectedHourPrototype).filter('*'); // remove text nodes
        _el.attr('data-elementId', item.elementId); // tag this element
        var _removeLink = _el.find('a.fbh_remove_selected_item_link');
        var _editLink = _el.find('a.fbh_edit_selected_item_link');
        
        _el.find('.fbh_selected_days_label').html(item.label.toUpperCase());
        _el.find('.fbh_selected_time_label').html(toTimepickerString(item.openingDateTime)+" - "+toTimepickerString(item.closingDateTime));
        
        // needed reference for click handlers
        // FIXME: this may not refer to expected instance if there are multiple instances of FancyBusinessHours
        _fbhInstance = this;
        
        // remove item handler
        _removeLink.attr('data-elementId', item.elementId)
            .click(function(){
                $(this).parents('.fbh_selected_item').remove();
                _fbhInstance.removeData(item.elementId);
                
                return false;
            });
        
        // edit selected item handler
        _editLink.attr('data-elementId', item.elementId)
            .click(function(){
                var _this = $(this);
                if (_this.hasClass('fbh_save_updated_data')) {
                    _this.find('i').removeClass('icon-ok').addClass('icon-edit');
                    _this.removeClass('fbh_save_updated_data');
                    var _widgetId = _this.attr('data-elementId');
                    var _widget = _fbhInstance.getWidget(_widgetId);
                    _fbhInstance.updateData(_widgetId, _fbhInstance._captureDataFromWidget(_widget)); // update the selected data
                    _fbhInstance.hideWidget(_widgetId); //
                }
                else {
                    _this.find('i').removeClass('icon-edit').addClass('icon-ok'); // change to ok icon
                    _this.addClass('fbh_save_updated_data');
                    _fbhInstance.showWidget(_this.attr('data-elementId')); // show widget
                }
                return false;
            });
        
        // add the notes
        _toggleNotes(_el.find('.fbh_selected_item_notes'),item.notes);
        _fbhInstance.dataContainer.append(_el);
        
        // we create a hidden widget for this item
        var _currentWidget = _fbhInstance.createNewWidget(item.elementId, item);
        _currentWidget.element.insertAfter(_el).hide();
        _currentWidget.addButton.remove(); // remove the button for this widget
    };
    
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
    
    var _toggleNotes = function(notesElement, notesData) {
        if ('' != notesData) {
            notesElement.html('<i class="icon-file-alt"></i>'+notesData).show();
        }
        else {
            notesElement.html('').hide();
        }
    };
    /** end private  functions **/
    
    return FancyBusinessHours;
    
};// end FancyBusinessHours
//----------------------------------------------------------------------------------------------------------------------------------------------------------

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
        notesElement: null,
        element: null, // element that represents this whole widget
        uuid: null, // identifier for this widget
        addButton: null
    };
    
    FancyBusinessHourWidget._owner = owner; // owner of this widget
    
    /**
     * Initialize selector widget
     */
    FancyBusinessHourWidget.initialize = function(days, openingDateTime, closingDateTime, notes){
        // get the prototype
        this.element = $(this._owner.selectorWidgetPrototype).filter('*'); // filter possible textnodes
        if (this.uuid != null) {
            this.element.attr('data-elementId', this.uuid);
        }
        
        this.notesElement = this.element.find('.fbh_notes').val(notes || ''); // set the notes element
        
        // init the timepicker elements
        this.openingTimePicker = this.element.find('.fbh_timepicker_opening');
        this.closingTimePicker = this.element.find('.fbh_timepicker_closing');
        
        // there is an initial data for opening and closing time
        if ((openingDateTime && openingDateTime instanceof Date  )  &&  (closingDateTime && closingDateTime instanceof Date)) {
            this.openingTimePickerData = openingDateTime;
            this.closingTimePickerData = closingDateTime;
        }
        else {
            // no initial data, set to current time
            this.openingTimePickerData = new Date();
            this.closingTimePickerData = new Date();
            this.setDefaultData();
        }
        this._initializeTimepickerWidget(this.openingTimePicker, this.openingTimePickerData); // init the opening time widget
        this._initializeTimepickerWidget(this.closingTimePicker, this.closingTimePickerData); // init the closing time widget
        // -- end timepicker initialization
        
        // initialize on click of add button
        _thatWidgetInstance = this;
        this.addButton = this.element.find('.fbh_add_button');
        this.addButton.click(function(){
            
            // get the widget uuid of the selector parent of this button
            var _widgetId = $(this).parents('.fbh_selector_widget').attr('data-elementId');
            
            // _thatWidgetInstance will refer to the last FancyBusinessHourWidget that got instantiated
            // but we can assume that they will share the same owner...
            var _widget = _thatWidgetInstance._owner.getWidget(_widgetId);
            var _data = _widget._owner._captureDataFromWidget(_widget);
            if (_data) {
                _widget._owner.addData(_data.weekdays, _data.openingDateTime, _data.closingDateTime, _data.notes);
            }
            _widget.resetForm();
            
            return false;
        });
        // -- end button click handler
        
        // initialize checkboxes
        if (days) {
            var _currWidget = this;
            $.each(days, function(_key, _day){
                _currWidget.element.find('input.fbh_weekdays[value='+_day+']').attr('checked', true);
            });
        }
        
        return this;
    }; // -- end of initialize
    
    FancyBusinessHourWidget.setDefaultData = function(){
     
        
        this.openingTimePickerData.setHours(8); // default to 8am
        this.openingTimePickerData.setMinutes(0); // reset minutes to 0
        this.openingTimePickerData.setSeconds(0); // reset seconds to 0
        this.openingTimePickerData.setMilliseconds(0); // reset milliseconds to 0
        //this.closingTimePickerData = new Date(this.openingTimePickerData.getTime()+(60*MINUTES)); // set to one hour from opening time
        
        this.closingTimePickerData.setHours(17); // defaut to 5pm
        this.closingTimePickerData.setMinutes(0); // reset minutes to 0
        this.closingTimePickerData.setSeconds(0); // reset seconds to 0
        this.closingTimePickerData.setMilliseconds(0); // reset milliseconds to 0
    };
    
    FancyBusinessHourWidget.resetForm = function(){
        this.element.find('input.fbh_weekdays:checked').attr('checked', false);
        this.notesElement.val('');
        this.setDefaultData();
        this.openingTimePicker.val(toTimepickerString(this.openingTimePickerData));
        this.closingTimePicker.val(toTimepickerString(this.closingTimePickerData));
    };
    
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
//----------------------------------------------------------------------------------------------------------------------------------------------------------