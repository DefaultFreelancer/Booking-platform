<template>
    <div class="admin-layout-margin" id="dashboard">
        <div id='dashboard_first_row' class="row margin-fix">
            <div class="col s12 m6 l6 xl3">
                <div class="card">
                    <div class="card-content">
                        <div class="dashboard-card-parent">
                            <div class="dashboard-card-child">
                                <div class="deep-purple accent-2 dashboard-top-row-parent">
                                    <i class="material-icons dp48 dashboard-top-row-child">assignment</i>
                                </div>
                            </div>
                            <div class="dashboard-card-child2">
                                <h5 class="dashboard-card-child">{{totalBooking}}</h5>
                                <small class="grey-text">{{ trans('lang.total_booking') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l6 xl3">
                <div class="card">
                    <div class="card-content">
                        <div class="dashboard-card-parent">
                            <div class="dashboard-card-child">
                                <div class="orange lighten-2 dashboard-top-row-parent">
                                    <i class="material-icons dp48 dashboard-top-row-child">local_mall</i>
                                </div>
                            </div>
                            <div class="dashboard-card-child2">
                                <h5 class="dashboard-card-child">{{pendingBooking}}</h5>
                                <small class="grey-text">{{ trans('lang.booking_pending') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l6 xl3">
                <div class="card">
                    <div class="card-content">
                        <div class="dashboard-card-parent">
                            <div class="dashboard-card-child">
                                <div class="cyan lighten-1 dashboard-top-row-parent">
                                    <i class="material-icons dp48 dashboard-top-row-child">stars</i>
                                </div>
                            </div>
                            <div class="dashboard-card-child2">
                                <h5 class="dashboard-card-child">{{confirmBooking}}</h5>
                                <small class="grey-text">{{ trans('lang.booking_confirm') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 m6 l6 xl3">
                <div class="card">
                    <div class="card-content">
                        <div class="dashboard-card-parent">
                            <div class="dashboard-card-child">
                                <div class="red accent-3 dashboard-top-row-parent">
                                    <i class="material-icons dp48 dashboard-top-row-child">content_paste</i>
                                </div>
                            </div>
                            <div class="dashboard-card-child2">
                                <h5 class="dashboard-card-child">{{cancelBooking}}</h5>
                                <small class="grey-text">{{ trans('lang.booking_cancel') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id='dashboard_second_row' class="row margin-fix">
            <div class="col s12 m12 l12 xl12">
                <div class="main-layout-card">
                    <div class="main-layout-card-header-with-button">
                        <div class="main-layout-card-content-wrapper">
                            <div class="main-layout-card-header-contents">
                                <h5 class="no-margin">{{ trans('lang.recent_bookings') }}</h5>
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
                        }
                    ],
                    source: '/bookinglistclient',
                    sortedBy: 'title',
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
        created() {
            this.getData('/clientdashboarddata');
        },
        mounted() {
            let instance = this;
            $('select').formSelect();

            $('#status').on('change', function () {
                let value = $(this).val();
                instance.status = value
                instance.readData();
            });
        },
        methods:
            {
                getPreloader: function (e, f) {
                    this.preloaderType = e;
                    this.hidePreloader = f;
                },
                getData(route) {
                    let instance = this;
                    this.axiosGet(route,
                        function (response) {
                            instance.item = response.data;
                            instance.totalBooking = instance.item.totalBooking;
                            instance.confirmBooking = instance.item.confirmBooking;
                            instance.pendingBooking = instance.item.pendingBooking;
                            instance.cancelBooking = instance.item.cancelBooking;

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
            }
    }
</script>