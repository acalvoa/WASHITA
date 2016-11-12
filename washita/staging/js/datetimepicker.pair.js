// Pair of controls: Botstrap datetimepicker and time combobox
// depends on datetimepicker.js
function PickupTime(from, to){
    this.from = from;
    this.to = to;
}
PickupTime.prototype.asText = function(){
        return this.from.format('DD/MM/YYYY HH:mm')+" |"+this.to.format('DD/MM/YYYY HH:mm');
}

function DateTimePickerPair(datetimepickerName, timeComboboxName, minDateTime, disableWeekends, disabledDates){
      var self = this;
      var datetimepickerName = datetimepickerName;
      var timeComboboxName = timeComboboxName;
      this.minDateTime = minDateTime;
      this.TIME = $("#"+timeComboboxName);
      this.disableWeekends = disableWeekends;
      this.disabledDates = disabledDates;

      var daysOfWeekDisabled = [];
      if(disableWeekends){
        daysOfWeekDisabled = [0, 6]; //Sat, Sun
      }


      
      this.DP  = $('#'+datetimepickerName).datetimepicker(
                            {
                                format: 'DD/MM/YYYY', 
                                locale: 'es',
                                defaultDate: self.minDateTime,
                                minDate: moment(self.minDateTime).startOf('day'),
                                maxDate: moment(self.minDateTime).startOf('day').add(3, 'months'),
                                daysOfWeekDisabled: daysOfWeekDisabled,
                                disabledDates: disabledDates || []
                            }
                    );
      
      this.DP.on("dp.change", function(e) {
            var choosenDate = e.date.toDate();
            self.updateHoursSelection(choosenDate);

            function isMomentWeekDay(mDate){
                var day = mDate.isoWeekday();
                return day == 6 || day == 7;
            }

            function isMomentNonWorkingDay(mDate){
                var isNonWorkingDay = false;
                self.disabledDates.forEach(function(disabledDate){
                    if(disabledDate.isSame(mDate, 'year') &&
                       disabledDate.isSame(mDate, 'month') &&
                       disabledDate.isSame(mDate, 'day')
                    ){
                        isNonWorkingDay = true;
                    }
                });

                return isNonWorkingDay;
            }

            function getAvailableDaytime(pickupDaytime){
                var newDate = moment(pickupDaytime);
                while(isMomentWeekDay(newDate) ||
                      isMomentNonWorkingDay(newDate)){

                    newDate = newDate.add(1, "day");
                }


                return  newDate;
            } 
            if(self.disableWeekends && isMomentWeekDay(e.date) || self.disabledDates){
                var newDateTime = getAvailableDaytime(e.date.toDate()); 
                $('#'+datetimepickerName+ ' input').val(moment(newDateTime).format("DD/MM/YYYY"));
            }
        });    
        
                  
      this.updateHoursSelection = function(choosenDate) {
            var choosenMoment = moment(choosenDate).startOf('day');
            
            var disableMorningOption = 
                (moment(this.minDateTime).startOf('day').diff(choosenMoment, 'days') === 0 && //same date
                 this.minDateTime.hours() >= 16 /* evening */);
                
            var morningOption = this.TIME.find("option[value='08:00-10:00']");  
            var eveningOption = this.TIME.find("option[value='16:00-18:00']");
              
            if(disableMorningOption){
                morningOption.attr('disabled', 'true');
                morningOption.addClass('disabledTime');
                //bug in Safari, using 'prop' instead of 'attr' for an item selection
                eveningOption.prop('selected', true);
            }
            else{
                morningOption.removeAttr('disabled');
                morningOption.removeClass('disabledTime');
            } 

            self.updateValue();
     } 
     
     this.updateValue = function(){
           var selectedTimeText = this.TIME.find("option:selected").val();
           var selectedTimeArray = selectedTimeText.split('-');
           
           var dpValue = moment(this.DP.data("DateTimePicker").date()).startOf('day');
           var from = selectedTimeArray[0].trim().split(':');
           var to = selectedTimeArray[1].trim().split(':');
           var value = new PickupTime(
               moment(dpValue).hour(from[0]).minutes(from[1]),
               moment(dpValue).hour(to[0]).minutes(to[1])
           )
           $(this).trigger("dpp.change", value); 
     }
     
     this.minDate = function(minDate){
         this.minDateTime = minDate;
         this.DP.data("DateTimePicker").minDate(moment(minDate).startOf('day'));

         self.updateHoursSelection(minDate);
     }

     this.setDateAndTime = function(choosenDate){
         this.DP.data("DateTimePicker").date(moment(choosenDate).startOf('day'));

         var timeSelect = choosenDate.hours() < 16
                        ? this.TIME.find("option[value='08:00-10:00']")
                        : this.TIME.find("option[value='16:00-18:00']");
         //bug in Safari, using 'prop' instead of 'attr' for an item selection
         timeSelect.prop('selected', true);
         this.updateHoursSelection(choosenDate);
     }
    
     this.TIME.change(function() {
        self.updateValue();
     });
     
     this.updateHoursSelection(this.DP.data("DateTimePicker").date());
}
