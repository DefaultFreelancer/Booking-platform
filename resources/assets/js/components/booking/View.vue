<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.booking_details') }}
        </div>
        <div class="admin-layout-margin">
            <div class="row margin-fix">
                <div class="col s12 m12 l8 xl8 no-padding">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <h5 class="bluish-text no-margin">{{ trans('lang.booking_details') + ' #'+
                                        bookingDetails.id }}</h5>
                                </div>
                                <div class="main-layout-card-header-contents right-align">
                                    <a href="#" @click="bookingsclick">{{ trans('lang.all_bookings') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                                <div class="modal-loader-child">
                                    <preloaders :loderType="preloaderType"></preloaders>
                                </div>
                            </div>
                            <div v-else>
                                <div class="booking-details-view">{{ trans('lang.service') }}</div>
                                <div class="booking-details-view">{{ bookingDetails.title }}</div>

                                <div class="booking-details-view">{{ trans('lang.status') }}</div>
                                <div class="booking-details-view" v-if="bookingDetails.status === 'confirmed'"><span
                                        class="booking-status green">{{ trans('lang.confirmed_') }}</span></div>
                                <div class="booking-details-view" v-if="bookingDetails.status === 'pending'"><span
                                        class="booking-status orange">{{ trans('lang.pending') }}</span></div>
                                <div class="booking-details-view" v-if="bookingDetails.status === 'canceled'"><span
                                        class="booking-status grey">{{ trans('lang.canceled') }}</span></div>

                                <div class="booking-details-view">{{ trans('lang.booking_date') }}</div>
                                <div class="booking-details-view">{{ bookingDetails.booking_date }}</div>

                                <div class="booking-details-view"
                                     v-if="bookingDetails.service_duration_type === 'hourly'">{{
                                    trans('lang.booking_time') }}
                                </div>
                                <div class="booking-details-view"
                                     v-if="bookingDetails.service_duration_type === 'hourly'">
                                    <div v-for="timeSlot in bookingDetails.booking_time">
                                        {{timeSlot}}
                                    </div>
                                </div>
                                <div class="booking-details-view">{{ trans('lang.quantity') }}</div>
                                <div class="booking-details-view">{{ bookingDetails.quantity }}</div>

                                <div class="booking-details-view">{{ trans('lang.bill') }}</div>
                                <div class="booking-details-view">{{ bookingDetails.bill }}</div>

                                <div class="booking-details-view">{{ trans('lang.payment_status') }}</div>
                                <div class="booking-details-view" v-if="bookingDetails.payment_stat <= 0">
                                    <div class="icon-wrapper">
                                        <div class="icon-wrapper-button">
                                            <span class="booking-status green">{{ trans('lang.paid') }}</span>
                                        </div>
                                        <div class="icon-wrapper-icon">
                                       <span class="booking-status">
                                            <a data-target="payment-modal" data-toggle="tooltip" data-position="bottom"
                                               :data-tooltip="trans('lang.payment_info')" class="modal-trigger mob-btn"
                                               href="#payment-modal" @click.prevent="isActive = 1">
                                                <i class="la la-info-circle la-2x"></i>
                                            </a>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="booking-details-view" v-else>
                                    <span class="booking-status red">{{ trans('lang.due') }}</span>
                                </div>

                                <div class="booking-details-view" v-if="bookingDetails.comment">{{ trans('lang.comment')
                                    }}
                                </div>
                                <div class="booking-details-view" v-if="bookingDetails.comment">{{
                                    bookingDetails.comment }}
                                </div>

                                <div class="booking-details-view">{{ trans('lang.created_at') }}</div>
                                <div class="booking-details-view">{{ localTime(bookingDetails.created_at) }}</div>

                                <!--<div >{{ trans('lang.service') }}</div>-->
                                <div class="booking-details-view-buttons">
                                    <button v-if="bookingDetails.status === 'pending'"
                                            class="btn waves-effect waves-light mob-btn green lighten-1" type="submit"
                                            @click.prevent="confirm(bookingDetails.id)">{{ trans('lang.confirm') }}
                                    </button>
                                    <button v-if="bookingDetails.status === 'pending' && bookingDetails.payment_stat > 0"
                                            class="btn waves-effect waves-light mob-btn red accent-2 modal-trigger"
                                            data-target="confirm-cancel-booking" @click.prevent="">{{
                                        trans('lang.cancel') }}
                                    </button>
                                    <button v-if="bookingDetails.status != 'canceled' && bookingDetails.payment_stat > 0"
                                            class="btn waves-effect waves-light cyan lighten-1 modal-trigger mob-btn"
                                            data-target="due-payment-modal" @click.prevent="isActive = 1">{{
                                        trans('lang.pay')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l4 xl4 no-padding">
                    <div class="main-layout-card main-layout-card-second">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <div class="row margin-fix center-align profile-image-container">
                                        <img class="responsive-img profile-image materialboxed"
                                             :src="publicPath+'/uploads/profile/'+booking.avatar" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <div class="row margin-fix center-align">
                                <h5 class="bluish-text">
                                    <a href="#" @click.prevent="userDetails(booking.user_id)">
                                        {{booking.first_name + ' '+ booking.last_name }}
                                    </a>
                                </h5>
                                <h6>
                                    <i class="la la-envelope"></i>
                                    {{ booking.email }}
                                </h6>
                                <h6 v-if="bookingDetails.phone_number !=null">
                                    <i class="la la-phone"></i>
                                    {{ bookingDetails.phone_number }}
                                </h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="payment-modal" class="modal modal-layout">
                <booking-payment-modal v-if="isActive == 1"
                                       :payment_method_id="bookingDetails.payment_id"></booking-payment-modal>
            </div>

            <div id="due-payment-modal" class="modal modal-layout">
                <due-payment v-if="isActive == 1" :booking_id="bookingDetails.id"></due-payment>
            </div>
            <div id="confirm-cancel-booking" class="modal modal-layout confirm-delete-modal">
                <confirmation-modal :message="'booking_will_be_canceled'" :firstButtonName="'yes'"
                                    :secondButtonName="'no'"
                                    @confirmationModalButtonAction="confirmationModalButtonAction"></confirmation-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,
        props: ['booking'],
        data() {
            return {
                preloaderType: 'load',
                hidePreloader: 'hide',
                isActive: 0,
                bookingDetails: '',
                paymentId: '',
                id:''
            }
        },
        mounted() {
            // $(function () {
            //     $('[data-toggle="tooltip"]').tooltip()
            // }),


            $('.materialboxed').materialbox();

            let instance = this;
            $('#due-payment-modal').modal({
                complete: function () {
                    instance.isActive = 0;
                },
            });
            $('#payment-modal').modal({
                complete: function () {
                    instance.isActive = 0;
                },
            });
            $('#confirm-cancel-booking').modal();
            if (this.booking) {
                this.bookingDetails = this.booking;
            }
            this.$hub.$on('getNewData', function () {
                instance.getNewData();
            });
        },
        methods: {
            confirmationModalButtonAction() {
                this.cancel(this.bookingDetails.id);
            },
            userDetails(id) {
                let instance = this;
                instance.axiosGet('/client/' + id,
                    function (response) {
                        users: this.booking.user_id;
                        // $('.tooltipped').tooltip({delay: 50});
                    }, function (response) {
                    });
                this.redirect("/client/" + id);

            },
            localTime(date_time) {
                return moment(date_time + ' Z', 'YYYY-MM-DD HH:mm:ss Z', true).format(this.dateTimeFormat);
            },
            confirm(id) {
                let instance = this;
                this.hidePreloader = '';
                instance.axiosPost('/actionbooking/' + id, {
                        status: 'confirmed'
                    },
                    function (response) {
                        M.toast({html: instance.trans('lang.booking_confirmed')});
                        instance.hidePreloader = 'hide';
                        instance.getNewData();
                    },
                    function (error) {
                        instance.errors = error.data.errors;
                        instance.hidePreloader = 'hide';
                        M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'})
                    });
            },
            cancel(id) {
                let instance = this;
                this.hidePreloader = '';

                instance.axiosPost('/actionbooking/' + id, {
                        status: 'canceled'

                    },
                    function (response) {
                        M.toast({html: instance.trans('lang.booking_canceled')});
                        instance.getNewData();
                    },
                    function (error) {
                        instance.errors = error.data.errors
                        instance.hidePreloader = 'hide';
                        M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'})
                    });
            },
            getNewData() {
                let instance = this;
                this.hidePreloader = '';
                instance.axiosGet('/booking-details/' + this.bookingDetails.id + '/true',
                    function (response) {
                        instance.bookingDetails = response.data;
                        instance.hidePreloader = 'hide';
                    }, function (response) {
                        instance.hidePreloader = 'hide';
                    })
            },
            bookingsclick() {
                let instance = this;
                instance.redirect('/bookings');
            },
        }
    }
</script>