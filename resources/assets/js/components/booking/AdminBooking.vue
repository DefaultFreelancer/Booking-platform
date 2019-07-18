<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin">{{ trans('lang.add_booking') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setActive()"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-layout-content" :class="{'modal-layout-fixed-height':!confirmBook}">
            <div v-if="circleLoader" class="center-align">
                <div class="modal-loader-parent">
                    <div class="modal-loader-child">
                        <preloaders :loderType="'load'"></preloaders>
                        <div v-if="bookingProcessing">
                            <h5 class="bluish-text">
                                {{ trans('lang.booking_is_processing') }}
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
            <div v-show="!circleLoader">
                <div class="row" v-show="!confirmBook">
                    <div class="input-field col s12 m6 offset-m3">
                        <select v-model="service_list" id="select">
                            <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                            <option v-for="service in services" :value="service.id"> {{service.title}}</option>
                        </select>
                        <label for="select">{{ trans('lang.service') }}</label>
                        <p v-if="errorInvalid.service_list" class="red-text select-tag-error">{{errorMessage.service_list}}</p>
                    </div>
                    <div class="input-field col s12 m6 offset-m3">
                        <!--<input id="date" v-model="service_starting_date" type="text"-->
                               <!--class="datepicker">-->
                        <input id="service_date" v-model="booking_date" type="text" class="datepicker" :class="{'invalid': errorInvalid.booking_date}" :disabled="!service_list">
                        <label for="service_date" :data-error="errorMessage.booking_date" :class="{'active': error}">{{ trans('lang.booking_date') }}</label>
                    </div>
                </div>
                <div class="row margin-fix" v-if="booking_date && !confirmBook">
                    <div class="col s12 m6 offset-m3" v-show="serviceTiming.length>0">
                        <div class="time-slot-space-wrapper center-align">
                            <a href="#" class="bluish-text time-slot-space" @click.prevent="decreaseSeat()">
                                <i class="material-icons dp48">remove_circle_outline</i>
                            </a>
                            <div class="input-field time-slot-space" v-if="selectedServiceDurationType == 'hourly'">
                                <input v-model="seat" type="text" id="space" class="center-align" :class="{'seat-increase-decrease':increaseDecreaseAnimate}" @animationend="increaseDecreaseAnimate = false" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57">
                                <label for="space" class="center-label">{{ trans('lang.your_desired_quantity') }}</label>
                            </div>
                            <div class="input-field time-slot-space" v-else>
                                <input v-model="seat" type="text" id="space" class="center-align" :class="{'seat-increase-decrease':increaseDecreaseAnimate}" @animationend="increaseDecreaseAnimate = false" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" @input="checkAvailableSeatForDaily">
                                <label for="space" class="center-label">{{ trans('lang.your_desired_quantity') }}</label>
                            </div>
                            <a href="#" class="bluish-text time-slot-space" @click.prevent="increaseSeat()">
                                <i class="material-icons dp48">add_circle_outline</i>
                            </a>
                        </div>
                        <div class="time-slot-wrapper" v-if="selectedServiceDurationType == 'hourly'">
                            <div class="time-slot-container" v-for="(time,index) in this.serviceTiming">
                                <a href="#" class="time-slot waves-effect waves-light"  @click.prevent="setSelectedTimeSlot(time)" :class="{'selectedTime': checkSelectedTimeSlot(time), 'disabledTimeSlot': disabledTimeSlot[index]}">
                                    <span v-if="selectedServiceDurationType === 'hourly'">{{ trans('lang.book_time') }}</span>
                                    <span v-else>{{ trans('lang.book_date') }}</span> : {{ time }}
                                    <span v-if="disabledTimeSlot[index] && available[index]>=0"> - {{ trans('lang.available') }} : {{available[index]}}</span>
                                </a>

                            </div>
                        </div>
                        <div class="time-slot-button">
                            <button class="btn waves-effect waves-light bluish-back" @click.prevent="changeConfirm(true),renderTextFields(), renderTextarea()" :disabled="checkIfTimeIsSelected() && selectedServiceDurationType == 'hourly'">{{ trans('lang.next_') }}</button>
                        </div>
                    </div>
                    <div class='center-align time-slot-loader' v-if="serviceTiming.length<=0">
                        <circle-loader></circle-loader>
                    </div>
                </div>
                <div class="row margin-fix" v-if="confirmBook">
                    <div class="col s12">
                        <h6>{{ trans('lang.customer_details') }}</h6>
                    </div>
                    <div class="row margin-fix">
                        <div class="input-field col s12 m6">
                            <input id="first_name" v-model="first_name" type="text" class="validate" :class="{'invalid': errorInvalid.first_name}" @input="errorInvalid.first_name = false">
                            <label for="first_name"  :class="{'active': error}">{{ trans('lang.first_name') }}</label>
                            <span class="helper-text" :data-error="errorMessage.first_name"></span>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="last_name" v-model="last_name" type="text" class="validate" :class="{'invalid': errorInvalid.last_name}" @input="errorInvalid.last_name = false">
                            <label for="last_name"  :class="{'active': error}">{{ trans('lang.last_name') }}</label>
                            <span class="helper-text" :data-error="errorMessage.last_name"></span>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="email" v-model="email" type="email" class="validate " :class="{'invalid': errorInvalid.email}" @input="errorInvalid.email = false, errorMessage.email = trans('lang.invalid_email')">
                            <label for="email"  :class="{'active': error}">{{ trans('lang.login_email') }}</label>
                            <span class="helper-text" :data-error="errorMessage.email"></span>
                        </div>
                        <div class="input-field col s12 m6">
                            <input id="phone_number" v-model="phone_number" type="text" class="validate" :class="{'invalid': errorInvalid.phone_number}" @input="errorInvalid.phone_number = false">
                            <label for="phone_number" :data-error="errorMessage.phone_number" :class="{'active': error}">{{ trans('lang.phone_number') }}</label>
                        </div>
                        <div class="input-field col s12">
                            <textarea id="comment" v-model="comment" class="materialize-textarea"></textarea>
                            <label for="comment">{{ trans('lang.comment') }}</label>
                        </div>
                        <div class="input-field col s12">
                            <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="setErrorInvalid(), is_disable(),book()" :disabled="is_disabled">{{ trans('lang.book') }}</button>
                            <button class="btn cancel-btn waves-effect waves-light modal-close mob-btn" @click.prevent="setActive()">{{ trans('lang.cancel') }}</button>
                            <button class="btn cancel-btn grey lighten-1 waves-effect waves-light mob-btn" @click.prevent="changeConfirm(false),setErrorInvalid(),renderTextFields()"> {{ trans('lang.back_') }} </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default{
        extends: axiosGetPost,
        data(){
            return{
                first_name:'',
                last_name:'',
                email:'',
                phone_number:'',
                comment:'',
                service_list:'',
                services:'',
                selectedServiceDurationType:'',
                isSubmitted:false,
                serviceTiming:[],
                slot:[],
                seat:1,
                booking_date:'',
                avSeat:true,
                offdays:[],
                holydays:[],
                available:[],
                checkMultiBooking:undefined,
                saveSlot:[],
                duration_type:0,
                max_day:'',
                from:'',
                to:'',
                max_day_before:'',
                error: false,
                errorInvalid: {
                    first_name: false,
                    last_name: false,
                    email: false,
                    slot: false,
                    seat: false,
                    service_list: false,
                    booking_date: false,
                    phone_number: false,
                },
                errorMessage: {
                    first_name: this.trans('lang.required_input_field'),
                    last_name: this.trans('lang.required_input_field'),
                    email: this.trans('lang.required_input_field'),
                    phone_number: this.trans('lang.required_input_field'),
                    slot: this.trans('lang.choose_available_time_slot'),
                    seat: this.trans('lang.booking_seat_can_not_be_zero'),
                    service_list: this.trans('lang.please_choose_one'),
                    booking_date: this.trans('lang.please_select_a_date')
                },
                is_disabled:false,
                confirmBook:false,
                selectedTimeSlot:[],
                disabledTimeSlot:[],
                circleLoader:true,
                bookingProcessing:false,
                increaseDecreaseAnimate:'',

            }
        },
        created(){
            this.getServices();
        },
        mounted()
        {
            M.updateTextFields();
            var instance = this;
            $('select').formSelect();
            $('input[type="checkbox"]').on('change', function()
            {
                $('input[name="' + this.name + '"]').not(this).prop('checked', false);
            });
            // Initialize error when inputing value

            $('#select').on('change', function(){
                let value = $(this).val();
                instance.errorInvalid.service_list = false;
                //instance.service_list = false;
                instance.serviceTiming=[];
                instance.disableTimeSlot();
                instance.services.forEach(function (i) {
                    if(i.id == value)
                    {
                        instance.duration_type= i.service_duration_type;
                        instance.max_day_before = i.allow_booking_max_day_ago;
                        if(i.service_starting_date)
                        {
                            var today= moment().format('YYYY-MM-DD');
                            var sDate = moment(i.service_starting_date).format('YYYY-MM-DD');

                            if(today>sDate)
                            {
                                instance.from = 'Today';
                            }
                            else
                            {
                                instance.from = i.service_starting_date.split('-');
                                instance.from[1] = instance.from[1]-1;
                            }

                        }
                        else
                        {
                            instance.from = 'Today';
                        }
                        if(!i.service_ending_date)
                        {
                            instance.max_day = (moment().add(i.allow_booking_max_day_ago-1, 'days').format("YYYY/MM/DD")).split('/');//making date to array
                            instance.max_day[1] = instance.max_day[1]-1;//month starts from 0. but june is 6.
                        }
                        else
                        {
                            instance.max_day= i.service_ending_date.split('-');
                            instance.max_day[1] = instance.max_day[1]-1;
                        }

                        let disableCondition = '',day;
                        for(day of instance.offdays){
                            if(disableCondition === ''){
                                disableCondition += 'date.getDay()=='+(day-1);
                            }else{
                                disableCondition += ' || date.getDay()=='+(day-1);
                            }
                        }

                       if(i.service_starting_date !=null && i.service_ending_date !=null){
                           $("#service_date").datepicker({
                               minDate: new Date(i.service_starting_date),
                               maxDate: new Date(i.service_ending_date),
                               format: instance.dateFormat,
                               disableDayFn: function(date) {
                                   if (eval(disableCondition))
                                       return true;
                                   else
                                       return false;
                               }
                           });
                       }else if(i.service_starting_date !=null){
                           $("#service_date").datepicker({
                               minDate: new Date(i.service_starting_date),
                               format: instance.dateFormat,
                               disableDayFn: function(date) {
                                   if (eval(disableCondition))
                                       return true;
                                   else
                                       return false;
                               }
                           });
                       }else if(i.service_ending_date){
                           $("#service_date").datepicker({
                               minDate:new Date(),
                               maxDate: new Date(i.service_ending_date),
                               format: instance.dateFormat,
                               disableDayFn: function(date) {
                                   if (eval(disableCondition))
                                       return true;
                                   else
                                       return false;
                               }
                           });
                       }
                       else{
                           $("#service_date").datepicker({
                               minDate:new Date(),
                               format: instance.dateFormat,
                               disableDayFn: function(date) {
                                   if (eval(disableCondition))
                                       return true;
                                   else
                                       return false;
                               }
                           });
                       }
                    }
                });
                instance.renderDatePicker();

            });
            $('#service_date').on('change', function(){
                instance.errorInvalid.booking_date = false;
                instance.disableTimeSlot();
            });
        },
        methods:{
            renderDatePicker()
            {
                var instance = this;
                var $input = $('#date').datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    highlight: 'Today',
                    format: instance.dateFormat,
                    closeOnSelect: true,// Close upon selecting a date,
                });
            },
            checkAvailableSeatForDaily(event)
            {
                if(parseInt(event.target.value)>=this.available[0])
                {
                    this.seat = this.available[0];
                }
                else
                {
                    this.seat = event.target.value;
                }
            },
            decreaseSeat(){
                if(this.seat<=1)
                {
                    this.seat=1;
                }
                else
                {
                    this.seat--;
                    this.increaseDecreaseAnimate = true;
                }
                this.disableTimeSlot();
            },
            increaseSeat(){
                this.seat++;
                if(this.selectedServiceDurationType != 'hourly' && this.seat >=this.available[0])
                {
                    this.seat = this.available[0]
                }
                this.disableTimeSlot();
                this.increaseDecreaseAnimate = true;
                this.$nextTick(function(){
                    M.updateTextFields();
                })
            },
            changeConfirm(value)
            {
                this.confirmBook = value;
            },
            disableTimeSlot()
            {
                var instance = this;
                for(var i=0; i<this.serviceTiming.length; i++)
                {
                    if(this.seat>this.available[i])
                    {
                        this.disabledTimeSlot[i] = true;
                        if(_.includes(this.selectedTimeSlot,this.serviceTiming[i]))
                        {
                            this.selectedTimeSlot.splice( _.findIndex(this.selectedTimeSlot,function(time) { return time == instance.serviceTiming[i]; }) , 1);
                        }
                    }
                    else{
                        this.disabledTimeSlot[i] = false;
                    }
                }
                M.updateTextFields();
            },
            setSelectedTimeSlot(time) //setting time slot into slot variable
            {
                var flag = 0;
                if(this.checkMultiBooking==1) //for multiple booking
                {
                    for(var i = 0; i<this.selectedTimeSlot.length; i++)
                    {
                        if( this.selectedTimeSlot[i] == time){
                            this.selectedTimeSlot.splice(i,1); // splicing time if same time is inserted again
                            flag=1;
                        }
                    }
                    if (flag==0)
                    {
                        this.selectedTimeSlot.push(time); // new selected time is pushed to array
                    }
                }
                else {                                  // for single booking
                    if(this.selectedTimeSlot.length<1)
                    {
                        this.selectedTimeSlot.push(time); // new selected time is pushed to array
                    }
                    else
                    {
                        if( this.selectedTimeSlot[0]==time) //if same time is inserted again
                        {
                            this.selectedTimeSlot=[]; //deselecting time by making the variable empty
                        }
                        else {
                            this.selectedTimeSlot=[];
                            this.selectedTimeSlot.push(time);  // new selected time is pushed to array
                        }
                    }
                    this.renderTextFields();
                }
                this.slot = this.selectedTimeSlot;

            },
            checkSelectedTimeSlot(time){
                if(_.includes(this.selectedTimeSlot,time))
                {
                    return true;
                }
                else
                {
                    return false;
                }
            },
            checkIfTimeIsSelected()
            {
                if(this.selectedTimeSlot.length<=0)
                {
                    return true;
                }
                else if(this.seat==0){
                    return true;
                }
                else
                {
                    return false;
                }

            },

            renderTextFields(){
                this.$nextTick(function () {
                    M.updateTextFields();
                });
            },
            renderTextarea()
            {
                this.$nextTick(function () {
                    $('#comment').trigger('autoresize');
                    $('textarea#comment').characterCounter();
                })
            },
            setActive: function() {
                this.$emit('setActive', 0);
            },

            getServices()
            {

                var instance = this;

                instance.axiosGet('/serviceshowforbooking', function(response){
                        instance.services = response.data.serviceData;
                        instance.offdays = response.data.offdays;
                        instance.holydays = response.data.holydays;
                        instance.holydays.forEach((hDay,index)=>{
                            if(hDay.start_date == hDay.end_date)
                            {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1])-1;
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);
                                instance.offdays.push(hDay.start_date)
                            }
                            else
                            {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1])-1;
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);
                                hDay.end_date = hDay.end_date.split("-");
                                hDay.end_date[0] = parseInt(hDay.end_date[0]);
                                hDay.end_date[1] = parseInt(hDay.end_date[1])-1;
                                hDay.end_date[2] = parseInt(hDay.end_date[2]);
                                instance.offdays.push({from:hDay.start_date,to:hDay.end_date})
                            }
                        })
                        instance.circleLoader=false;
                        instance.disableTimeSlot();
                        setTimeout(function () {
                            $('select').formSelect();
                            $('#select').on('change', function() {
                                let value = $(this).val();
                                instance.service_list = value;
                                instance.slot=[]
                                instance.serviceTiming=[];
                                instance.disableTimeSlot();
                                if(instance.booking_date)
                                {
                                    instance.getTiming(value)
                                }
                                var i;
                                for (i = 0; i < instance.services.length; i++) {
                                    if(instance.services[i].id==value)
                                    {
                                        instance.checkMultiBooking = instance.services[i].multiple_bookings
                                        instance.selectedServiceDurationType = instance.services[i].service_duration_type
                                        break;
                                    }
                                }
                            });
                        })

                        $('#service_date').on('change', function () {
                            var dateOfBooking = $(this).val();
                            instance.booking_date = dateOfBooking;
                            instance.serviceTiming=[];
                            instance.getTiming(instance.service_list);
                            instance.disableTimeSlot();
                        });
                    },
                    function (response) {

                    })
            },
            setErrorInvalid(){
                this.error = false;
                this.errorInvalid.first_name = false;
                this.errorInvalid.last_name = false;
                this.errorInvalid.email = false;
                this.errorInvalid.slot = false;
                this.errorInvalid.seat = false;
                this.errorInvalid.service_list = false;
                this.errorInvalid.booking_date = false;
                this.errorInvalid.phone_number = false;
            },
            changeSlotError(){
                this.errorInvalid.slot = false;
            },
            setError(value){
                let instance = this;
                instance.$nextTick(function(){
                    instance.error = true;
                    instance.errorInvalid[value] = true;
                });
            },
            book(){
                var instance = this;
                var index;
                var minsSeat=999999;
                var i;
                // Service choosing option validation
                if(instance.service_list === '') {instance.setError("service_list");instance.is_disabled=false;}
                if(instance.service_list && instance.booking_date === '') {instance.setError("booking_date");instance.is_disabled=false;}
                if(instance.duration_type =='hourly' && instance.service_list && instance.booking_date && instance.slot.length == 0) {instance.setError("slot");instance.is_disabled=false;}
                if(instance.first_name === '') {instance.setError("first_name");instance.is_disabled=false;}
                if(instance.last_name === '') {instance.setError("last_name");instance.is_disabled=false;}
                var emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!instance.email.match(emailFormat)){
                    if(instance.email === ''){
                        instance.errorMessage.email = instance.trans('lang.required_input_field');
                    } else {
                        instance.errorMessage.email = instance.trans('lang.invalid_email');
                    }
                    instance.setError("email");
                }
                if(instance.avSeat == false){
                    instance.errorMessage.seat = instance.trans('lang.limited_seat');
                    instance.setError('seat');
                    instance.is_disabled=false;
                } else {
                    if(!instance.seat || instance.seat < 1) {instance.setError('seat');instance.is_disabled=false;}
                }
                if (instance.service_list && instance.booking_date && instance.email.match(emailFormat) && instance.avSeat && instance.seat > 0) {
                    if(instance.duration_type == 'hourly')
                    {
                        for(i =0; i<this.slot.length;i++)
                        {
                            instance.index = this.serviceTiming.indexOf(this.slot[i]);
                            if(this.available[instance.index]<minsSeat)
                            {
                                minsSeat = this.available[instance.index];
                            }
                        }
                    }
                    else
                    {
                        minsSeat = this.available[0];
                    }
                    if(this.seat>minsSeat)
                    {
                        this.avSeat=false;
                    }
                    else
                    {
                        instance.circleLoader=true;
                        instance.bookingProcessing=true;
                        instance.axiosPost('/bookservice/' + this.service_list,{
                                first_name: this.first_name,
                                last_name: this.last_name,
                                email: this.email,
                                phone_number: this.phone_number,
                                comment: this.comment,
                                seat: this.seat,
                                slot: this.slot,
                                booking_date: this.booking_date,
                                duration_type: this.duration_type,
                            },
                            function(response){
                                M.updateTextFields();
                                M.toast({html:instance.trans('lang.admin_booking_submitted_successfully')});

                                instance.slot=[];
                                instance.seat = 0;
                                instance.serviceTiming = [];
                                instance.available =[];
                                instance.getTiming(instance.service_list);
                                instance.disableTimeSlot();
                                instance.is_disabled = false;
                                instance.circleLoader=false;
                                $('#booking-modal').modal('close');
                                instance.setActive();
                                instance.$hub.$emit('reloadDataTable');
                            },
                            function (error) {
                                instance.errors = error.data.errors
                                M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                                instance.is_disabled = false;
                                instance.circleLoader=false;
                            });
                    }
                }
                else
                {
                    instance.is_disabled = false;
                }
            },
            getTiming(id)
            {
                let instance = this;
                if(instance.booking_date!='')
                {

                    instance.axiosGet('/servicetiming/'+id+'/'+moment(this.booking_date, 'MMMDDYYYY').format('YYYY-MM-DD'),
                        function(response){
                            instance.serviceTiming = response.data.stack;
                            instance.available = response.data.seat;
                            instance.disableTimeSlot();
                            instance.$nextTick(function () {
                                if( instance.selectedServiceDurationType == 'daily')
                                {
                                    instance.slot = instance.serviceTiming
                                }
                                else {
                                    $('#space').on('input', function () {
                                        let value = $(this).val();
                                        instance.seat = value;
                                        instance.disableTimeSlot();
                                    });
                                    instance.disableTimeSlot();
                                }
                                instance.seat = 1
                                instance.renderTextFields();
                            });
                        },
                        function (response) {

                        },
                    );
                }
            },
            is_disable()
            {
                this.is_disabled = true;
            }
        }
    }
</script>