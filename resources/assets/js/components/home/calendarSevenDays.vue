<template>
    <div class="row margin-fix">
        <div class="home-background"></div>
        <i class="material-icons icon-load">ac_unit</i><i class="la la-user la-1x icon-load"></i>
        <div class="row nav-bar-home-page">
            <div class=" col s12 m12 l10 offset-l1"
                 :class="{'xl9 offset-xl1': windowWidth>1200 & windowWidth <1306 ,'xl8 offset-xl2': windowWidth>=1306}">
                <a href="#" @click="homepage"> <img :src="publicPath+'/uploads/logo/'+appLogo" alt="" class="logo left"></a>
                <div v-if="user === null">
                    <a href="#" @click="register" class="sign-up right" v-if="checksignup.can_signup === 1">{{
                        trans('lang.sign_up') }}</a>
                    <a href="#" @click="login" class="sign-in right">{{ trans('lang.sign_in') }}</a>
                </div>
                <div v-else>
                    <a href="#" @click="logout" class="sign-up right">{{ trans('lang.logout_nv') }}</a>
                    <a href="#" @click="dashboard" class="sign-in right">{{ trans('lang.my_account') }}</a>
                </div>
            </div>
        </div>
        <div class="row margin-fix">
            <div class="col s12 m12 l10 offset-l1 home-contents"
                 :class="{'xl9 offset-xl1': windowWidth>1200 & windowWidth <1306 ,'xl8 offset-xl2': windowWidth>=1306}">
                <div v-if="!showCalendar && loadService" class="row animated fadeInDown" id="welcome-message">
                    <div class="welcome-message-wrapper">
                        <div class="welcome-message center-align white-text">
                            <span v-if="landing_page_header.length>0">
                                {{ landing_page_header }}
                            </span>
                            <span v-else>
                                {{ trans('lang.welcome_to_gain_booking')}}
                            </span>
                        </div>
                        <div class="landing_page_massage center-align short-description white-text welcome-message-description-landingPage ">
                            <span v-if="landing_page_message.length>0">
                                {{ landing_page_message }}
                            </span>
                            <span v-else>
                                {{ trans('lang.home_page_welcome_message')}}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="center-align home-page-loader" :class="{'home-page-circle': !loadService}" v-if="!showCalendar && loadingServices">
                    <circle-loader></circle-loader>
                </div>
                <div v-else class="center-align">
                    <div v-if="!showCalendar && loadService" class="row animated fadeInDown">
                        <div class="service-list"
                             @click.prevent="setServiceToSelectCalendar(service.id,service.multiple_bookings)"
                             v-for="service in services">
                            {{ service.title }}
                        </div>
                    </div>
                </div>
                <div class="row animated fadeInDown" v-if="showCalendar">
                    <div class="center-align service-details-wrapper" v-if="selectedService==service.id"
                         v-for="service in services">
                        <div class="selected-service-title white-text">
                            {{ service.title }}
                        </div>
                        <h6 class="white-text">
                            {{ service.description}}
                        </h6>
                    </div>
                </div>
                <div v-if="showCalendar && loadCalendar" class="row center-align">
                    <div v-if='windowWidth>=992' class="calendar-week-wrapper animated fadeInDown">
                        <div class="week-change-buttons">
                            <a href="#" @click.prevent="numberOfDays-=7" :class="{'isDisabled-arrow':numberOfDays<=7}">
                                <i class="date-changer la la-angle-left"></i>
                            </a>
                        </div>
                        <a href="#calendar_modal" class="calendar-view-date modal-trigger white-text"
                           :class="{'disabledDates': isDisabled([getDates(n-1)[3],getDates(n-1)[2],getDates(n-1)[1]],offdays,renderDayName(getDates(n-1))) }"
                           v-if="index >= (startIndex=numberOfDays-7)" v-for="(n,index) in  numberOfDays"
                           @click.prevent="showBookingForm=true,setDate=getDates(n-1),triggerModal(getDates(n-1),service_list)">
                            <div class="calendar-week-names center-align">
                                {{ trans('lang.'+ getDates(n-1)[0].toLowerCase())}}
                            </div>
                            <div class="center-align calendar-date-month" :class="{'bluish-text': checkToday(n-1)}">
                                <!--<div class="calendar-date-single">{{ getDates(n-1)[1] }}</div>-->
                                <div class="calendar-date-single"
                                     :class="{'show-new-date': index >= (startIndex=numberOfDays-7)}">{{
                                    getDates(n-1)[1] }}
                                </div>
                                <div class="calendar-month-single">{{ trans('lang.'+ getDates(n-1)[2].toLowerCase())}}</div>
                            </div>
                        </a>
                        <div class="week-change-buttons">
                            <a href="#" @click.prevent="numberOfDays+=7">
                                <i class=" date-changer la la-angle-right"></i>
                            </a>
                        </div>
                        <div class="center-align" :class="{'service-choose': showCalendar}"
                             @click.prevent="showCalendar=false,loadCalendar=false,numberOfDays=7" v-if="loadService">
                            <i class="la la-angle-left"></i>
                        </div>
                        <!--<div v-show="calendarLoaded()" class="animated fadeInUp">-->
                        <!---->
                        <!--</div>-->
                    </div>
                    <div v-else class="calendar-week-wrapper-mobile animated fadeInDown">
                        <div class="week-change-buttons-mobile">
                            <a href="#" @click.prevent="numberOfDays-=7" :class="{'isDisabled-arrow':numberOfDays<=7}">
                                <i class="date-changer la la-angle-left"></i>
                            </a>
                            <div class="center-align" :class="{'service-choose': showCalendar}"
                                 @click.prevent="showCalendar=false,loadCalendar=false,numberOfDays=7" v-if="loadService">
                                <i class="la la-angle-left"></i>
                            </div>
                            <a href="#" @click.prevent="numberOfDays+=7">
                                <i class="date-changer la la-angle-right"></i>
                            </a>
                        </div>
                        <a href="#calendar_modal" class="calendar-view-date-mobile modal-trigger"
                           :class="{'show-new-date': index >= (startIndex=numberOfDays-7) ,'disabledDates': isDisabled([getDates(n-1)[3],getDates(n-1)[2],getDates(n-1)[1]],offdays,renderDayName(getDates(n-1))) }"
                           v-if="index >= (startIndex=numberOfDays-7)" v-for="(n,index) in  numberOfDays"
                           @click.prevent="showBookingForm=true,setDate=getDates(n-1),triggerModal(getDates(n-1),service_list)">
                            <div :class="{'today-mob-date': checkToday(n-1)}">
                                {{ trans('lang.'+ getDates(n-1)[0].toLowerCase())}}
                                {{ getDates(n-1)[1] }}
                                {{ trans('lang.'+ getDates(n-1)[2].toLowerCase())}}
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Modal -->
        <div id="calendar_modal" class="modal">
            <div class="center-align" v-if="user === null && checksignup.submit_booking_after_login === 1">
                <div class="role-close-button modal-close">
                    <a href="#" @click.prevent=""><i
                            class="material-icons grey-text icon-vertically-middle">close</i></a>
                </div>
                <login-register-notice :checksignup="checksignup"></login-register-notice>
            </div>
            <div v-else class="content-parent">
                <div class="content-child">
                    <div v-for="service in services" v-if="showBookingForm">
                        <bookservie-form v-if="service.id == service_list"
                                         :serviceTitle="service.title"
                                         :servicePrice="service.price"
                                         :dateForBooking="dateForBooking(setDate)"
                                         :service_selected="service_list"
                                         :service_price="service.service_price"
                                         :checkMultiBooking="checkMultiBooking"
                                         :paymentMethods="paymentMethods"
                                         v-on:setBookingLoader="getBookingLoader"
                                         v-on:setRenderBookingForm="getRenderBookingForm"
                                         :user="user"
                                         :service="service"
                        ></bookservie-form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        props: ['sessionstatus', 'checksignup', 'user', 'landing_page_header','service', 'landing_page_message'],
        data() {
            return {
                paymentMethods: [],
                showBookingForm: false,
                setDate: '',
                months: moment.months(),
                sevenDays: [],
                today: moment().format("ddd-DD-MMM-YYYY").split('-'),
                thisDay: moment().format("D"),
                thisMonth: moment().format("MMMM"),
                thisMonthShort: moment().format("MMM"),
                thisMonthInt: moment().format("MM"),
                thisYear: moment().format("YYYY"),
                service_list: '',
                offdays: [],
                services: '',
                welcomeMessage: '',
                shortDescription: '',
                selectedService: '',
                hideBookingForm: true,
                checkMultiBooking: '',
                numberOfDays: 7,
                startIndex: 0,
                showCalendar: '',
                loadService:false,
                loadCalendar: false,
                loadingServices: true,
                windowWidth: 0,
                isDisabled: function (date, hDay, dayName) {
                    date[1] = moment().month(date[1]).format("MM");
                    var flag2 = false;
                    var today = new Date();
                    var dd = today.getDate();
                    var mm = today.getMonth() + 1; //January is 0!
                    var yyyy = today.getFullYear();
                    var allowMaxDay;
                    if (yyyy >= date[0]) {
                        if (mm == date[1]) {
                            if (dd > date[2]) {
                                flag2 = true;
                            }
                        }
                        if (mm > date[1]) {
                            flag2 = true;
                        }
                    }

                    hDay.forEach(function (day) {

                        if (typeof day == "number") {
                            if (day == 1 && dayName == 'Sunday') {
                                flag2 = true;
                            }
                            if (day == 2 && dayName == 'Monday') {
                                flag2 = true;
                            }
                            if (day == 3 && dayName == 'Tuesday') {
                                flag2 = true;
                            }
                            if (day == 4 && dayName == 'Wednesday') {
                                flag2 = true;
                            }
                            if (day == 5 && dayName == 'Thursday') {
                                flag2 = true;
                            }
                            if (day == 6 && dayName == 'Friday') {
                                flag2 = true;
                            }
                            if (day == 7 && dayName == 'Saturday') {
                                flag2 = true;
                            }
                        }
                        else if (Array.isArray(day)) {
                            if (date[2] == day[2] && date[1] == day[1] && date[0] == day[0]) {
                                flag2 = true;
                            }

                        }
                        else {
                            let dateJoined = date.join('-');
                            let dateFromJoined = day.from.join('-');
                            let dateToJoined = day.to.join('-');
                            if (moment(dateJoined, "YYYY-MM-DD") >= moment(dateFromJoined, "YYYY-MM-DD") && moment(dateJoined, "YYYY-MM-DD") <= moment(dateToJoined, "YYYY-MM-DD")) {
                                flag2 = true;
                            }
                        }
                    });
                    let instance = this;
                    let dateJoined = date.join('-');
                    this.services.forEach(function (service) {
                        if (service.id == instance.service_list) {
                            if (service.service_starting_date) {
                                if (moment(service.service_starting_date, "YYYY-MM-DD") <= moment(dateJoined, "YYYY-MM-DD") && moment(service.service_ending_date, "YYYY-MM-DD") >= moment(dateJoined, "YYYY-MM-DD")) {
                                }
                                else {
                                    flag2 = true;
                                }
                            }
                            if (service.allow_booking_max_day_ago) {
                                allowMaxDay = moment().add(service.allow_booking_max_day_ago - 1, 'days').format("YYYY-MM-DD");
                                if (moment(dateJoined, "YYYY-MM-DD") <= moment(allowMaxDay, "YYYY-MM-DD")) {

                                }
                                else {
                                    flag2 = true;
                                }
                            }
                        }
                    });
                    return flag2;

                },
            }
        },
        created() {
            this.getServices();
            this.getPaymentMethods();
        },
        mounted() {

            if(!this.service){
                this.loadService = true;
            }
            $('select').formSelect();

            this.$nextTick(function () {
                window.addEventListener('resize', this.getWindowWidth);
                this.getWindowWidth();
            });
            if (this.sessionstatus) {
                if (this.sessionstatus == "registration_disabled") M.toast({
                    html: this.trans('lang.registration_is_disabled'),
                    classes: 'red lighten-1'
                });
                if (this.sessionstatus == "mail_sent") M.toast({html: this.trans('lang.confirmation_email_send')});
                if (this.sessionstatus == "something_wrong") M.toast({
                    html: this.trans('lang.something_wrong'),
                    classes: 'red lighten-1'
                });
            }
        },
        methods: {
            calendarLoaded() {
                var value = false;
                setTimeout(function () {
                    value = true
                }, 1000);
                return value;
            },
            login() {
                let instance = this;
                instance.redirect('/login');
            },
            homepage() {
                let instance = this;
                instance.redirect('/');
            },
            register() {
                let instance = this;
                instance.redirect('/register')
            },
            logout() {
                let instance = this;
                instance.redirect('/logout');
            },
            dashboard() {
                let instance = this;
                instance.redirect('/dashboard');
            },
            triggerModal(date, service) {
                this.modalDate = date + "";
                this.service_list = service;
                $('#calendar_modal').modal();
                this.showBookingForm = true;
                this.hideBookingForm = true;

            },
            getBookingLoader: function (e) {
                this.hideBookingForm = e;

            },
            getRenderBookingForm: function (e) {
                this.showBookingForm = e;
            },
            getDates(n) {
                return this.sevenDays[n] = moment().add(n, 'day').format("ddd-DD-MMM-YYYY").split('-');
            },
            dateForBooking(setDate) {
                return setDate[3] + '-' + moment().month(setDate[2]).format('MM') + '-' + setDate[1];
            },
            checkToday(n) {
                return _.isEqual(this.sevenDays[n], this.today)
            },
            renderDayName: function (date) {
                return moment(date[1] + "/" + moment().month(date[2]).format("MMMM") + "/" + date[3], "DD/MMMM/YYYY").format('dddd');
            },

            getPaymentMethods() {
                var instance = this;
                this.axiosGet('/getpmethods',
                    function (response) {
                        instance.paymentMethods = response.data;
                    },
                    function (response) {
                    },
                );
            },
            getServices() {
                var instance = this;
                this.axiosGet('/serviceshowforbooking',
                    function (response) {
                        instance.services = response.data.serviceData;
                        instance.offdays = response.data.offdays;
                        instance.holydays = response.data.holydays;

                        instance.holydays.forEach((hDay, index) => {

                            if (hDay.start_date == hDay.end_date) {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1]);
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);
                                instance.offdays.push(hDay.start_date)
                            }
                            else {
                                hDay.start_date = hDay.start_date.split("-");
                                hDay.start_date[0] = parseInt(hDay.start_date[0]);
                                hDay.start_date[1] = parseInt(hDay.start_date[1]);
                                hDay.start_date[2] = parseInt(hDay.start_date[2]);

                                hDay.end_date = hDay.end_date.split("-");
                                hDay.end_date[0] = parseInt(hDay.end_date[0]);
                                hDay.end_date[1] = parseInt(hDay.end_date[1]);
                                hDay.end_date[2] = parseInt(hDay.end_date[2]);

                                instance.offdays.push({from: hDay.start_date, to: hDay.end_date})
                            }

                        })
                        instance.check = true;
                        $('select').formSelect();
                        $('select').on('change', function () {
                            let value = $(this).val();

                            instance.service_list = value;
                            var i;
                            for (i = 0; i < instance.services.length; i++) {
                                if (instance.services[i].id == value) {
                                    instance.checkMultiBooking = instance.services[i].multiple_bookings
                                    break;
                                }
                            }
                        });

                        instance.loadCalendar = true;
                        instance.showCalendar = false;
                        instance.loadingServices = false;

                        if(instance.service){
                            instance.setServiceToSelectCalendar(instance.service.id,instance.service.multiple_bookings);
                        }
                    },
                    function (response) {
                        instance.loadingServices = false;
                    },
                );

            },
            setServiceToSelectCalendar(id, multipleBooking) {
                this.showCalendar = true;
                this.service_list = id;
                this.checkMultiBooking = multipleBooking,
                this.selectedService = id;
                this.loadCalendar = true;
            },
            getWindowWidth(event) {
                this.windowWidth = document.documentElement.clientWidth;
            },
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.getWindowWidth);
        },
    }
</script>
