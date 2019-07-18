<template>
    <div>
        <!--<div class="navbar-text hide-on-med-and-down">-->
            <!--{{ trans('lang.client_bookings') }}-->
        <!--</div>-->
        <div class="admin-layout-margin" id="dashboard">
            <div id='dashboard_second_row' class="row margin-fix">
                <div class="col s12 m12 l12 xl12">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <h5 class="no-margin">{{ trans('lang.bookings') }}</h5>
                                </div>
                                <div class="main-layout-card-header-contents right-align">
                                    <a class="btn waves-effect waves-light bluish-back" href="#" @click="newbooking">{{
                                        trans('lang.new_booking') }}</a>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content">
                            <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                                <div class="modal-loader-child">
                                    <preloaders :loderType="preloaderType"></preloaders>
                                </div>
                            </div>
                            <div class="row margin-fix">
                                <datatable-component :options="tableOptions" @setPreloader="getPreloader"
                                                     v-show="hidePreloader=='hide'"></datatable-component>
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
            let instance = this;
            return {
                item: [0],
                totalBooking: 0,
                confirmBooking: 0,
                pendingBooking: 0,
                cancelBooking: 0,
                status: 0,
                from: 0,
                to: 0,
                hidePreloader: '',
                preloaderType: 'load',
                tableOptions: {
                    tableName: 'booking',
                    columns: [
                        {title: 'lang.id', key: 'id', type: 'text', sortable: true},
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
                        /*{title: 'lang.action', type: 'component', componentName: 'booking-action-component'}*/
                    ],
                    source: '/bookinglistclient',
                    //sortedBy: 'title',
                    sortingOrder: 'DESC',
                    search: false,
                    filters: [
                        {
                            title: 'lang.status', key: 'status', type: 'dropdown', options: [
                                {text: 'lang.all', value: 0, selected: true},
                                {text: 'lang.confirmed_', value: 1},
                                {text: 'lang.pending', value: 2},
                                {text: 'lang.canceled', value: 3}
                            ]
                        },
                        {title: 'lang.date_range', key: 'date_range', type: 'date_range'}
                    ]
                }

            }
        },
        mounted() {

            this.getData();

            let instance = this;
            $('select').formSelect();
            $('#status').on('change', function () {
                let value = $(this).val();
                instance.status = value
                instance.readData();
            });

            // Listen to event on datatable action component
            // this.bookingDetails()
        },
        methods: {
            getPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            getData() {
                let instance = this;
                this.axiosGet('/clientdashboarddata',
                    function (response) {
                        instance.item = response.data;
                        instance.totalBooking = instance.item.totalBooking;
                        instance.confirmBooking = instance.item.confirmBooking;
                        instance.pendingBooking = instance.item.pendingBooking;
                        instance.cancelBooking = instance.item.cancelBooking;
                        instance.$nextTick(function () {
                            M.updateTextFields();
                        });
                    },
                    function (response) {
                    },
                );
            },

            getDatefilterValue: function (fromDate, toDate) {
                this.from = fromDate;
                this.to = toDate;
                this.setPreloader('load', '');
                this.readData();
            },
            newbooking() {
                let instance = this;
                instance.redirect('/');
            }
        }
    }
</script>