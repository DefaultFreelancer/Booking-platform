<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.bookings') }}
        </div>
        <div class="admin-layout-margin">
            <div class="row main-layout-card">
                <div class="main-layout-card-header">
                    <div class="main-layout-card-content-wrapper">
                        <div class="main-layout-card-header-contents">
                            <h5 class="bluish-text no-margin">{{ trans('lang.bookings') }}</h5>
                        </div>
                        <div class="main-layout-card-header-contents right-align" v-if="bookper==1">
                            <button class="btn waves-effect waves-light bluish-back modal-trigger"
                                    data-target="booking-modal" @click=" isActive = 1">{{ trans('lang.add') }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="main-layout-card-content">
                    <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                        <div class="modal-loader-child">
                            <preloaders :loderType="preloaderType"></preloaders>
                        </div>
                    </div>
                    <div class="row margin-fix" v-show="hidePreloader=='hide'">
                        <datatable-component :options="tableOptions" @setPreloader="getPreloader"></datatable-component>
                        <!--<all-bookings v-if="isActive == 1" v-on:setPreloader="getPreloader"></all-bookings>-->
                    </div>
                </div>
            </div>
            <div id="booking-modal" class="modal modal-layout">
                <adminbooking-form v-if="isActive == 1 && bookper==1" v-on:setActive="getActive"></adminbooking-form>
            </div>
            <div id="due-payment-modal" class="modal modal-layout">
                <due-payment v-if="isActive == 2" :booking_id="duePaymentBookingId"></due-payment>
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
        props: ['bookper', 'clientsetting'],

        data() {
            let instance = this;
            return {
                isActive: 0,
                hidePreloader: '',
                preloaderType: 'load',
                items: [],
                status: 0,
                duration: 0,
                from: 0,
                to: 0,
                booking: [],
                book: '',
                id: '',
                duePaymentBookingId: '',
                cancelBookingId: '',
                tableOptions: {
                    tableName: 'booking',
                    columns: [
                        {
                            title: 'lang.id',
                            key: 'id',
                            type: 'clickable_link',
                            source: '/booking-details',
                            uniquefield: 'id',
                            sortable: true
                        },
                        {title: 'lang.service', key: 'title', type: 'text', sortable: true},
                        {
                            title: 'lang.status', key: 'status', type: 'custom', sortable: true,
                            modifier: function (rowData) {
                                if (rowData == 'confirmed') return {
                                    value: instance.trans('lang.confirmed_'),
                                    class: "booking-status green"
                                };
                                else if (rowData == 'pending') return {
                                    value: instance.trans('lang.pending'),
                                    class: "booking-status orange"
                                };
                                else if (rowData == 'canceled') return {
                                    value: instance.trans('lang.canceled'),
                                    class: "booking-status grey"
                                };
                            }
                        },
                        {title: 'lang.book_date', key: 'booking_date', type: 'text', sortable: true},
                        {title: 'lang.time', key: 'booking_time', type: 'array', sortable: false},
                        {title: 'lang.quantity', key: 'quantity', type: 'text', sortable: true},
                        (instance.clientsetting == 1 ? {
                                title: 'lang.client',
                                key: 'first_name',
                                type: 'clickable_link',
                                source: '/client',
                                uniquefield: 'user_id',
                                sortable: true
                            } :
                            {title: 'lang.client', key: 'first_name', type: 'text', sortable: true}),
                        {title: 'lang.bill', key: 'booking_bill', type: 'text', sortable: true},
                        {
                            title: 'lang.payment_status', key: 'payment_stat', type: 'custom', sortable: false,
                            modifier: function (rowData) {
                                if (rowData <= 0) return {
                                    value: instance.trans('lang.paid'),
                                    class: "booking-status green"
                                };
                                else return {value: instance.trans('lang.due'), class: "booking-status red"};
                            }
                        },
                        {title: 'lang.booking_invoice', type: 'component', componentName: 'booking-invoice-component'},
                        {title: 'lang.action', type: 'component', componentName: 'booking-action-component'}
                    ],
                    source: '/bookingshow',
                    // sortedBy: 'title',
                    sortingOrder: 'DESC',
                    search: true,
                    filters: [
                        {
                            title: 'lang.status', key: 'status', type: 'dropdown', options: [
                                {text: 'lang.all', value: 0, selected: true},
                                {text: 'lang.confirmed_', value: 1},
                                {text: 'lang.pending', value: 2},
                                {text: 'lang.canceled', value: 3}
                            ]
                        },
                        {
                            title: 'lang.payment_status', key: 'payment_status', type: 'dropdown', options: [
                                {text: 'lang.all', value: 0, selected: true},
                                {text: 'lang.due', value: 1},
                                {text: 'lang.paid', value: 2},
                            ]
                        },
                        {title: 'lang.date_range', key: 'date_range', type: 'date_range'}
                    ]
                }
            }
        },
        methods: {
            getActive: function (newActive) {
                this.isActive = newActive;
            },
            getPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            getDatefilterValue: function (fromDate, toDate) {
                this.from = fromDate;
                this.to = toDate;
                this.setPreloader('load', '');
                this.readData();
            },
            bookingDetails() {
                let instance = this;
                instance.$hub.$on('bookingDetails', function (id) {
                    instance.redirect("/booking-details/" + id);
                })
            },
            renderTooltip(t) {
                $('.tooltipped').tooltip(t);
            },
            duePayment(id) {
                this.duePaymentBookingId = id;
            },

            confirmationModalButtonAction() {
                this.cancelBooking(this.cancelBookingId)
            },
            cancelBooking(id) {
                var instance = this;
                instance.getPreloader('delete', '');

                instance.axiosPost('/actionbooking/' + id, {
                        status: 'canceled'
                    },
                    function (response) {
                        M.toast({html: instance.trans('lang.booking_is_canceled')});
                        instance.$hub.$emit('reloadDataTable');
                    },
                    function (error) {
                        instance.errors = error.data.errors;
                        instance.getPreloader('delete', 'hide');
                    });
            }
        },
        mounted() {
            let instance = this;
            this.$hub.$on('renderTooltip', function (value) {
                instance.renderTooltip(value);
            });
            $('#booking-modal').modal({
                complete: function () {
                    instance.getActive(0);
                    instance.renderTooltip('');
                },
            });
            $('#due-payment-modal').modal({
                complete: function () {
                    instance.renderTooltip('');
                    instance.getActive(0);
                },onCloseEnd:function () {
                    instance.getActive(0);
                }
            });
            $('#confirm-cancel-booking').modal();
            $('select').formSelect();

            $('#status').on('change', function () {
                let value = $(this).val();
                instance.status = value
                instance.readData();
            });
            instance.$hub.$on('getActive', function (isActive) {
                instance.getActive(isActive);
            });
            instance.$hub.$on('cancelBookingFromDatatable', function (id) {
                instance.cancelBookingId = id;
            });
            instance.$hub.$on('duePayment', function (id) {
                instance.duePayment(id);
            });

            // Listen to event on datatable action component
            this.bookingDetails();
            this.$hub.$on('renderTooltip', function (value) {
                instance.renderTooltip(value);
            });
        },
    }
</script>