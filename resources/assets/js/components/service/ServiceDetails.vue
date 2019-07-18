<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 v-if="serviceId==0" class="bluish-text no-margin">{{ trans('lang.add_service') }}</h5>
                <h5 v-else class="bluish-text no-margin">{{ trans('lang.update_service') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setActive(1,'')"><i
                        class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <form>
                <div class="row margin-fix">
                    <div class="input-field col s12 m12 l12">
                        <input v-model="title" name="title" id="title" type="text" class="validate"
                               :class="{'invalid': errorInvalid.title}" @input="errorInvalid.title=false">
                        <label for="title"  :class="{'active':error}">{{
                            trans('lang.title') }}</label>
                        <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div class="input-field col s12">
                        <textarea id="description" v-model="description" class="materialize-textarea service-description"></textarea>
                        <label for="description">{{ trans('lang.description') }}</label>
                    </div>
                    <div class="col s12 m4 l4">
                        <div class="input-field">
                            <div class="label-for-radio">
                                {{ trans('lang.service_duration_type') }}
                            </div>
                        </div>
                        <label for="hourly">
                            <input type="radio" id="hourly" class="with-gap" v-model="service_duration_type"
                                   value="hourly"/>
                            <span>{{ trans('lang.hourly') }}</span></label>
                        <label for="daily">
                            <input type="radio" id="daily" class="with-gap" v-model="service_duration_type"
                                   value="daily"/>
                            <span>{{ trans('lang.daily') }}</span></label>
                    </div>
                    <div class="input-field col s12 m4 l4">
                        <input id="service_starting_date" v-model="service_starting_date" type="text"
                               class="datepicker">
                        <label for="service_starting_date">{{ trans('lang.service_starting_date') }}</label>
                    </div>
                    <div class="input-field col s12 m4 l4">
                        <input id="service_ending_date" v-model="service_ending_date" type="text" class="datepicker"
                               :class="{'invalid': errorInvalid.service_ending_date}">
                        <label for="service_ending_date">{{
                            trans('lang.service_ending_date') }}</label>
                        <span class="helper-text" :data-error="serviceDateError"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m6 l4">
                        <div class="input-field">
                            <div class="label-for-radio">
                                {{ trans('lang.multiple_bookings') }}
                            </div>
                        </div>
                        <label for="multiYes">
                            <input type="radio" id="multiYes" class="with-gap" v-model="multiple_bookings" value="1"
                                   :disabled="service_duration_type=='daily'"/>
                            <span :class="{'default-cursor':service_duration_type === 'daily'}">{{ trans('lang.yes') }}</span></label>
                        <label for="multiNo">
                            <input type="radio" id="multiNo" class="with-gap" v-model="multiple_bookings" value="0"
                                   :disabled="service_duration_type=='daily'"/>
                            <span :class="{'default-cursor':service_duration_type === 'daily'}">{{ trans('lang.no') }}</span></label>
                    </div>
                    <div class="input-field col s12 m6 l4">
                        <input v-model="available_seat" name="available_seat" id="available_seat" type="text"
                               class="validate" :class="{'invalid': errorInvalid.available_seat}"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"
                               @input="errorInvalid.available_seat=false">
                        <label for="available_seat"
                               :class="{'active':error}">{{ trans('lang.available_seat') }}</label>
                        <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div v-if="decimalSeparator == '.'" class="input-field col s12 m12 l4">
                        <input v-model="price" name="price" id="price" type="text"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57 || event.charCode == 46"
                               @input="errorInvalid.price=false" class="validate"
                               :class="{'invalid': errorInvalid.price}">
                        <label for="price"  :class="{'active':error}">{{
                            trans('lang.price_per_space') }}</label>
                        <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div v-if="decimalSeparator == ','" class="input-field col s12 m12 l4">
                        <input v-model="price" name="price" id="priceComma" type="text"
                               onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57 || event.charCode == 44"
                               @input="errorInvalid.price=false" class="validate"
                               :class="{'invalid': errorInvalid.price}">
                        <label for="priceComma"
                               :class="{'active':error}">{{ trans('lang.price_per_space') }}</label>
                        <span class="helper-text":data-error="trans('lang.required_input_field')"></span>
                    </div>
                </div>
                <div v-if="service_duration_type == 'hourly'" class="row margin-fix">
                    <div class="input-field col s12 m12 l4">
                        <input v-model="service_starts" :placeholder="getPalceholder()" name="service_starts"
                               id="service_starts" type="text" class="validate"
                               :class="{'invalid': errorInvalid.service_starts}">
                        <label for="service_starts"
                               :class="{'active':error}">{{ trans('lang.service_starts') }}</label>
                        <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div class="input-field col s12 m12 l4">
                        <input v-model="service_ends" :placeholder="getPalceholder()" name="service_ends"
                               id="service_ends" type="text" class="validate"
                               :class="{'invalid': errorInvalid.service_ends}">
                        <label for="service_ends"  :class="{'active':error}">{{
                            trans('lang.service_ends') }}</label>
                        <span class="helper-text" :data-error="serviceEndError"></span>
                    </div>

                    <div class="input-field col s12 m12 l4">
                        <input v-model="service_duration" :placeholder="serviceDurationPlaceholder"
                               name="service_duration" id="service_duration" type="text" class="validate"
                               :class="{'invalid': errorInvalid.service_duration}">
                        <label for="service_duration"  :class="{'active':error}">{{
                            trans('lang.service_duration') }}</label>
                        <span class="helper-text" :data-error="serviceDurationError"></span>
                    </div>
                </div>
                <div class="row margin-fix">
                    <div class="input-field col s12">
                        <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit"
                                @click.prevent="initializeError(), is_disable(),createService()"
                                :disabled="is_disabled">{{ trans('lang.save') }}
                        </button>
                        <button class="btn cancel-btn waves-effect waves-light mob-btn modal-close"
                                @click.prevent="setActive(1,'')">{{ trans('lang.cancel') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        props: ['serviceId', 'precision'],
        data() {
            return {
                title: '',
                price: '',
                payment_method: '',
                allow_cancel: '',
                auto_confirm: '',
                allow_coupons: '',
                service_duration: '',
                service_starts: '',
                service_ends: '',
                multiple_bookings: 0,
                available_seat: '',
                description: '',
                min_booking: 0,
                max_booking: 0,
                week_start: '',
                activation: '',
                service_duration_type: 'hourly',
                service_starting_date: null,
                service_ending_date: null,
                isSubmitted: false,
                is_disabled: false,
                serviceDurationPlaceholder: '',
                preloaderType: '',
                hidePreloader: '',
                serviceEndError: this.trans('lang.required_input_field'),
                serviceDurationError: this.trans('lang.required_input_field'),
                serviceDateError: '',
                validDateRange: true,
                getPalceholder() {

                    if (this.timeFormat == '12h') {
                        this.serviceDurationPlaceholder = 'hh:mm'
                        return "hh:mm AM/PM (12h)"
                    }
                    else {
                        this.serviceDurationPlaceholder = 'hh:mm'
                        return "hh:mm (24h)"
                    }

                },
                error: false,
                errorInvalid: {
                    title: false,
                    price: false,
                    available_seat: false,
                    service_starts: false,
                    service_ends: false,
                    service_duration: false,
                    service_ending_date: false,
                }
            }
        },
        created() {

        },
        computed: {

            validation: function () {
                return {
                    title: !!this.title.trim(),
                    price: !!this.price,
                    service_duration: !!this.service_duration.trim(),
                    // min_booking: !!this.min_booking.trim(),
                    // max_booking: !!this.max_booking.trim(),
                    service_starts: !!this.service_starts.trim(),
                    service_ends: !!this.service_ends.trim(),
                    available_seat: !!this.available_seat,
                };
            },
            isValid: function () {
                let validation = this.validation;
                return Object.keys(validation).every(function (key) {
                    return validation[key]
                })
            },
        },
        mounted() {
            this.readData();
            let instance = this;
            $("#service_starting_date").datepicker({
                minDate:new Date(),
                format: instance.dateFormat,
            });

            $('#service_starting_date').on('change', function () {
                var value = $(this).val();
                instance.service_starting_date = value;
            });
            $("#service_ending_date").datepicker({
                minDate:new Date(),
                format: instance.dateFormat,
            });
            $('#service_ending_date').on('change', function () {
                var value = $(this).val();
                instance.service_ending_date = value;
            });

            $('#service_duration,#min_booking,#max_booking').timepicker({'timeFormat': 'H:i'});
            $('#service_duration,#min_booking,#max_booking').click(function () {
                $('.ui-timepicker-wrapper').css("width", $('#service_duration').width() + "px");
                $('.ui-timepicker-wrapper').css("width", $('#min_booking').width() + "px");
                $('.ui-timepicker-wrapper').css("width", $('#max_booking').width() + "px");
            });
            $('#service_starts').on('changeTime', function () {
                let value = $(this).val();
                instance.service_starts = value;
                // Initialize error on Input
                instance.errorInvalid.service_starts = false;
            });
            $('#service_ends').on('changeTime', function () {
                let value = $(this).val();
                instance.service_ends = value;
                // Initialize error on Input
                instance.errorInvalid.service_ends = false;
            });
            $('#service_duration').on('changeTime', function () {
                let value = $(this).val();
                instance.service_duration = value;
                // Initialize error on Input
                instance.errorInvalid.service_duration = false;
            });
            $('#daily,#hourly').on('change', function () {
                if (instance.errorInvalid.title == true || instance.errorInvalid.price == true || instance.errorInvalid.available_seat == true) {
                    instance.initializeError();
                    if (instance.title === '') {
                        instance.setError('title');
                    }
                    if (instance.price === '') {
                        instance.setError('price');
                    }
                    if (instance.available_seat === '') {
                        instance.setError('available_seat');
                    }
                }
                instance.$nextTick(function () {
                    M.updateTextFields();
                    $('#service_duration,#min_booking,#max_booking').timepicker({'timeFormat': 'H:i'});
                    $('#service_starts,#service_ends').timepicker({'timeFormat': 'h:i A'});
                    $('#service_duration,#service_starts,#service_ends').click(function () {
                        $('.ui-timepicker-wrapper').css("width", $('#service_duration,#service_starts,#service_ends').width() + "px");
                    });
                    $('#service_starts').on('changeTime', function () {
                        let value = $(this).val();
                        instance.service_starts = value;
                        // Initialize error on Input
                        instance.errorInvalid.service_starts = false;
                    });
                    $('#service_ends').on('changeTime', function () {
                        let value = $(this).val();
                        instance.service_ends = value;
                        // Initialize error on Input
                        instance.errorInvalid.service_ends = false;
                    });
                    $('#service_duration').on('changeTime', function () {
                        let value = $(this).val();
                        instance.service_duration = value;
                        // Initialize error on Input
                        instance.errorInvalid.service_duration = false;
                    });
                })
                let value = $(this).val();
                if (instance.service_duration_type == 'daily') {
                    instance.multiple_bookings = 0;
                }
            });

            // Initialize Error on Input
        },
        methods: {
            setActive: function (e, f) {
                this.$emit('setActive', e, f);
            },
            setPreloader: function (type, action) {
                //this.$emit('setPreloader', e,f);
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            initializeError() {
                this.error = false;
                this.errorInvalid.title = false;
                this.errorInvalid.price = false;
                this.errorInvalid.available_seat = false;
                this.errorInvalid.service_starts = false;
                this.errorInvalid.service_ends = false;
                this.errorInvalid.service_duration = false;
                this.errorInvalid.service_ending_date = false;
            },
            setError(value) {
                let instance = this;
                instance.$nextTick(function () {
                    instance.error = true;
                    instance.errorInvalid[value] = true;
                });
                instance.setPreloader('load', 'hide');
            },
            createService() {
                let instance = this;
                // Validate all required field
                if (instance.title === '') {
                    instance.setError('title');
                }
                if (instance.price === '') {
                    instance.setError('price');
                }
                if (instance.available_seat === '') {
                    instance.setError('available_seat');
                }

                if (instance.service_starting_date > instance.service_ending_date) {
                    instance.setError('service_ending_date');
                    instance.serviceDateError = instance.trans('lang.cant_be_less_then_start_date');
                    instance.validDateRange = false;
                }
                else {
                    instance.validDateRange = true;
                }

                if (instance.service_duration_type != 'daily') {
                    if (instance.service_starts === '') {
                        instance.setError('service_starts');
                    }
                    if (instance.service_ends === '') {
                        instance.setError('service_ends');
                        instance.serviceStartError = instance.trans('lang.required_input_field');
                    }
                    if (instance.service_duration === '') {
                        instance.setError('service_duration');
                        instance.serviceDurationError = instance.trans('lang.required_input_field');
                    }
                    if (moment(instance.service_starts, "h:mm A").format('HH:mm') >= moment(instance.service_ends, "h:mm A").format('HH:mm')) {
                        if (instance.service_ends === '') {
                            instance.setError('service_ends');
                            instance.serviceStartError = instance.trans('lang.required_input_field');
                        }
                        else {
                            instance.setError('service_ends');
                            instance.serviceEndError = instance.trans('lang.cannot_be_equal_or_less_than_start');
                        }
                    }
                    if (instance.service_duration === '00:00') {
                        instance.setError('service_duration');
                        instance.serviceDurationError = instance.trans('lang.service_duration_cannot_be_0_mins');
                    }
                }
                if (instance.validDateRange && instance.service_duration_type == 'daily' && instance.title && instance.price && instance.available_seat) {
                    this.saveData();
                }
                else if (instance.validDateRange && instance.service_duration_type == 'hourly' && instance.title && instance.price && instance.available_seat && instance.service_starts && instance.service_ends && instance.service_duration && instance.service_duration != '00:00' && (moment(instance.service_starts, "h:mm A").format('HH:mm') < moment(instance.service_ends, "h:mm A").format('HH:mm'))) {
                    this.saveData();
                }
                else {
                    instance.is_disabled = false;
                }
            },

            saveData() {
                let instance = this;
                instance.setPreloader('load', '');
                if (this.serviceId == 0) {
                    instance.axiosPost('/servicenew/create/store', {
                            title: this.title,
                            price: accounting.unformat(this.price, this.decimalSeparator),
                            service_duration: this.service_duration,
                            multiple_bookings: this.multiple_bookings,
                            description: this.description,
                            min_booking: this.min_booking,
                            max_booking: this.max_booking,
                            service_starts: this.service_starts,
                            service_ends: this.service_ends,
                            available_seat: this.available_seat,
                            service_duration_type: this.service_duration_type,
                            service_starting_date: instance.service_starting_date,
                            service_ending_date:  instance.service_ending_date
                        },
                        function (response) {
                            instance.setPreloader('load', 'hide')
                            M.toast({html: instance.trans('lang.service_successfully_updated')});
                            instance.setActive(1, 'save');
                            instance.is_disabled = false;
                        },
                        function (error) {
                            instance.setPreloader('load', 'hide')
                            instance.errors = error.data.errors
                            M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'});
                            instance.is_disabled = false;

                        });

                }
                else {
                    instance.axiosPost('/serviceid/' + this.serviceId, {
                            title: this.title,
                            price: accounting.unformat(this.price, this.decimalSeparator),
                            service_duration: this.service_duration,
                            multiple_bookings: this.multiple_bookings,
                            description: this.description,
                            min_booking: this.min_booking,
                            max_booking: this.max_booking,
                            service_starts: this.service_starts,
                            service_ends: this.service_ends,
                            available_seat: this.available_seat,
                            service_duration_type: this.service_duration_type,
                            service_starting_date:instance.service_starting_date,
                            service_ending_date:instance.service_ending_date,
                        },
                        function (response) {
                            instance.setPreloader('load', 'hide')
                            M.toast({html: instance.trans('lang.service_successfully_updated')});
                            instance.setActive(1, 'save');
                            instance.is_disabled = false;
                        },
                        function (error) {
                            instance.setPreloader('load', 'hide')
                            instance.errors = error.data.errors
                            M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'});
                            instance.is_disabled = false;

                        });
                }

            },
            readData() {
                let instance = this;
                instance.setPreloader('load', '');

                setTimeout(function () {
                    M.updateTextFields();
                    instance.renderTextarea();
                });

                if (instance.timeFormat == '12h') {
                    $('#service_starts').timepicker({'timeFormat': 'h:i A'});
                    $('#service_ends').timepicker({'timeFormat': 'h:i A'});
                }
                else {
                    $('#service_starts').timepicker({'timeFormat': 'H:i'});
                    $('#service_ends').timepicker({'timeFormat': 'H:i'});
                }
                $('#service_starts').click(function () {
                    $('.ui-timepicker-wrapper').css("width", $('#service_starts').width() + "px");
                });
                $('#service_ends').click(function () {
                    $('.ui-timepicker-wrapper').css("width", $('#service_ends').width() + "px");
                });

                if (this.serviceId != 0) {
                    this.axiosGet('/serviceid/' + this.serviceId,
                        function (response) {
                            instance.title = _.clone(response.data.title);
                            instance.price = accounting.formatNumber(_.clone(response.data.price), instance.precision, instance.thousandSeparator, instance.decimalSeparator);
                            //this.price = this.item.price;
                            instance.service_duration = _.clone(response.data.service_duration);
                            instance.multiple_bookings = _.clone(response.data.multiple_bookings);
                            instance.description = _.clone(response.data.description);
                            instance.service_starts = _.clone(response.data.service_starts);
                            instance.service_ends = _.clone(response.data.service_ends);
                            instance.available_seat = _.clone(response.data.available_seat);
                            instance.service_duration_type = _.clone(response.data.service_duration_type);
                            if (response.data.service_starting_date != null) {
                                instance.service_starting_date = moment(response.data.service_starting_date, 'YYYY-MM-DD').format(instance.dateFormat.toUpperCase())
                            }
                            if (response.data.service_ending_date != null) {
                                instance.service_ending_date = moment(response.data.service_ending_date, 'YYYY-MM-DD').format(instance.dateFormat.toUpperCase())
                            }
                            instance.$nextTick(function () {
                                M.updateTextFields();
                            });
                            setTimeout(function () {
                                M.updateTextFields();
                                $('select').formSelect();
                                $("#service_starting_date").datepicker({
                                    minDate:new Date(),
                                    format: instance.dateFormat,
                                })
                                $("#service_ending_date").datepicker({
                                    minDate:new Date(),
                                    format: instance.dateFormat,
                                });
                            })

                            instance.renderDatePicker();
                            instance.setPreloader('load', 'hide');
                        },
                        function (response) {
                        },
                    );

                }
                else {
                    setTimeout(function () {
                        M.updateTextFields();
                        $('select').formSelect();
                    })
                    instance.renderDatePicker();
                    instance.setPreloader('load', 'hide');
                }
            },
            renderTextarea() {
                this.$nextTick(function () {
                    $('#description').trigger('autoresize');
                    $('textarea#description').characterCounter();
                })
            },
            renderDatePicker() {
                let instance = this;
                $('.datepicker').datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    format: instance.dateFormat,
                    highlight: 'Today',
                    disable:[true],
                    closeOnSelect: true, // Close upon selecting a date,
                    min: [moment().format("YYYY"), moment().format("MM") - 1, moment().format("DD")]
                });
                instance.$nextTick(function () {
                    M.updateTextFields();
                });
            },
            is_disable() {
                this.is_disabled = true;
            },
        },
    }
</script>
