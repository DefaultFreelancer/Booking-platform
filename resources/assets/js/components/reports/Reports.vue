<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.reports') }}
        </div>
        <div class="admin-layout-margin">
            <div class="main-layout-card">
                <div class="main-layout-card-header">
                    <div class="main-layout-card-content-wrapper">
                        <div class="main-layout-card-header-contents">
                            <h5 class="bluish-text no-margin">{{ trans('lang.reports') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="main-layout-card-content">
                    <div class="row margin-fix">
                        <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                            <div class="modal-loader-child">
                                <preloaders :loderType="preloaderType"></preloaders>
                            </div>
                        </div>
                        <div v-show="hidePreloader=='hide'">
                            <div class="row">
                                <div class="input-field col s12 m6">
                                    <select v-model="service_list" id="select">
                                        <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                                        <option v-for="service in services" :value="service.id"> {{service.title}}
                                        </option>
                                    </select>
                                </div>
                                <div class="input-field col s12 m6">
                                    <div v-show="service_list">
                                        <input id="date" v-model="booking_date" type="text" class="datepicker">
                                        <label for="date">{{ trans('lang.booking_date') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div id="renderChart" v-if="hidePreloader=='hide' && serviceTiming.length > 0 && count > 0"
                                 class="row">
                                <report-bar-chart ref="barchart" :width="400" :height="582"
                                                  v-if="serviceTiming.length > 0" :data="chartData"></report-bar-chart>
                            </div>
                            <div class="col s12" v-else-if="hidePreloader=='hide' && serviceTiming.length">
                                <h6 class="center-align"> {{ trans('lang.didnt_find') }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        data() {
            return {
                service_list: '',
                services: [],
                serviceTiming: [],
                slot: [],
                seat: 1,
                booking_date: '',
                avSeat: true,
                offdays: [],
                holydays: [],
                confirm: [],
                pending: [],
                checkMultiBooking: undefined,
                saveSlot: [],
                availableSeat: [],
                preloaderType: 'load',
                hidePreloader: '',
                error: false,
                // for Bar Chart
                chartData: {},
                count: '',
                showCircleLoader: true
            }
        },
        created() {
            this.getServices();

        },
        mounted() {
            let instance = this;
            $('.datepicker').datepicker({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 70, // Creates a dropdown of 70 years to control year,
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                format: instance.dateFormat,
                closeOnSelect: true, // Close upon selecting a date,
                disable: instance.offdays, // disabled for sunday
                highlight: 'Today',
                i18n: {},
            });

            this.getTodaysDate();

        },
        methods: {
            setPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            getTodaysDate() {
                let today = new Date(),
                    day = today.getDate(),
                    month = today.getMonth() + 1,
                    year = today.getFullYear();
                // Add zero before the value of less than 10
                if (day < 10) day = '0' + day;
                if (month < 10) month = '0' + month;
                // Date formatting
                today = year + '-' + month + '-' + day;
                this.booking_date = moment(today, 'YYYY-MM-DD').format(this.dateFormat.toUpperCase());
            },
            getServices() {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/serviceshowforbooking',
                    function (response) {
                        instance.services = response.data.serviceData;
                        instance.offdays = response.data.offdays;
                        instance.holydays = response.data.holydays;
                        instance.holydays.forEach((hDay, index) => {
                            if (hDay.start_date == hDay.end_date) {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1]) - 1;
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);
                                instance.offdays.push(hDay.start_date)
                            }
                            else {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1]) - 1;
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);
                                hDay.end_date = hDay.end_date.split("-");
                                hDay.end_date[0] = parseInt(hDay.end_date[0]);
                                hDay.end_date[1] = parseInt(hDay.end_date[1]) - 1;
                                hDay.end_date[2] = parseInt(hDay.end_date[2]);
                                instance.offdays.push({from: hDay.start_date, to: hDay.end_date})
                            }
                        })
                        // select first service from service list
                        if (instance.services.length > 0) {
                            instance.services.forEach((e) => {
                                if (!instance.service_list) instance.service_list = e.id;
                            })
                            if (instance.booking_date) instance.getTiming(instance.service_list)
                        }
                        instance.setPreloader('load', 'hide');

                        $('select').on('change', function () {
                            let value = $(this).val();
                            instance.service_list = value;
                            instance.slot = []
                            if (instance.booking_date) {
                                instance.getTiming(value)
                            }
                            var i;
                            for (i = 0; i < instance.services.length; i++) {
                                if (instance.services[i].id == value) {
                                    instance.checkMultiBooking = instance.services[i].multiple_bookings
                                    break;
                                }
                            }
                        });
                        $('.datepicker').on('change', function () {
                            var dateOfBooking = $(this).val();
                            instance.booking_date = dateOfBooking;
                            instance.getTiming(instance.service_list)
                        });

                        setTimeout(function () {
                            $('select').formSelect();
                        });
                    },
                    function (response) {
                    },
                );
            },
            // time slot with confirm pending seat
            getTiming(id) {
                let instance = this;
                instance.setPreloader('load', '');
                this.axiosGet('/booking-report/' + id + '/' + moment(instance.booking_date, instance.dateFormat.toUpperCase()).format('YYYY-MM-DD'),
                    function (response) {
                        instance.serviceTiming = response.data.stack;
                        instance.confirm = response.data.confirmSeat;
                        instance.pending = response.data.pendingSeat;
                        instance.availableSeat = response.data.availableSeat;
                        instance.count = response.data.count;
                        instance.chartData = {
                            labels: instance.serviceTiming,
                            datasets: [
                                {
                                    label: instance.trans('lang.confirmed_booking'),
                                    backgroundColor: '#4CAF50',
                                    data: instance.confirm
                                },
                                {
                                    label: instance.trans('lang.pending_booking'),
                                    backgroundColor: '#ff9800',
                                    data: instance.pending
                                }
                            ]
                        };
                        instance.setPreloader('load', 'hide');
                    },
                    function (response) {
                        instance.setPreloader('load', 'hide');
                    },
                );
            },
        }
    }
</script>