<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.dashboard') }}
        </div>
        <div class="admin-layout-margin-for-col" id="dashboard">
            <div id='dashboard_first_row' class="row margin-fix">
                <div class="col s12 m6 l6 xl3 col-right-padding">
                    <div class="card">
                        <div class="card-content">
                            <div class="dashboard-card-parent">
                                <div class="dashboard-card-child">
                                    <div class="deep-purple accent-2 dashboard-top-row-parent">
                                        <i class="material-icons dp48 dashboard-top-row-child">assignment</i>
                                    </div>
                                </div>
                                <div class="dashboard-card-child2">
                                    <h5 class="dashboard-card-child">{{totalAllBooking}}</h5>
                                    <div class="dashboard-card-small-details grey-text">{{
                                        trans('lang.total_booking_for_next_30_days') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m6 l6 xl3 col-right-padding">
                    <div class="card">
                        <div class="card-content">
                            <div class="dashboard-card-parent">
                                <div class="dashboard-card-child">
                                    <div class="orange lighten-2 dashboard-top-row-parent">
                                        <i class="material-icons dp48 dashboard-top-row-child">local_mall</i>
                                    </div>
                                </div>
                                <div class="dashboard-card-child2">
                                    <h5 class="dashboard-card-child">{{totalBooking}}</h5>
                                    <div class="dashboard-card-small-details grey-text">{{
                                        trans('lang.confirmed_booking_for_next_30_days') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m6 l6 xl3 col-right-padding">
                    <div class="card">
                        <div class="card-content">
                            <div class="dashboard-card-parent">
                                <div class="dashboard-card-child">
                                    <div class="cyan lighten-1 dashboard-top-row-parent">
                                        <i class="material-icons dp48 dashboard-top-row-child">stars</i>
                                    </div>
                                </div>
                                <div class="dashboard-card-child2">
                                    <h5 class="dashboard-card-child">{{todaysBooking}}</h5>
                                    <div class="dashboard-card-small-details grey-text">{{
                                        trans('lang.today_total_booking') }}
                                    </div>
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
                                    <h5 class="dashboard-card-child">{{todaysBookingPending}}</h5>
                                    <div class="dashboard-card-small-details grey-text">{{
                                        trans('lang.today_pending_booking') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id='dashboard_second_row' class="row margin-fix">
                <div class="col s12 m12 l12 xl9 col-right-padding">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <h5 class="no-margin">{{ trans('lang.booking_overview') }}</h5>
                                    <small class="grey-text">{{ trans('lang.last_12_months') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content chartjs-loader-wrapper chart-height">
                        <span v-if="hideChartLoader">
                            <div class="chartjs-loader center-align">
                                 <circle-loader></circle-loader>
                            </div>
                        </span>
                            <span v-else><line-chart :width="400" :height="405" :linedata="lineChartItem"></line-chart></span>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l12 xl3">
                    <div class="main-layout-card">
                        <div class="main-layout-card-header-with-button">
                            <div class="main-layout-card-content-wrapper">
                                <div class="main-layout-card-header-contents">
                                    <h5 class="no-margin">{{ trans('lang.booking_type') }}</h5>
                                    <small class="grey-text">{{ trans('lang.total_bookings_of_different_services') }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="main-layout-card-content chartjs-loader-wrapper chart-height">
                        <span v-if="hideChartLoader">
                            <div class="chartjs-loader center-align">
                                <circle-loader></circle-loader>
                            </div>
                        </span>
                            <span v-else>
                            <doughnut-chart :width="200" :height="405" :confirmed="confirmedPercentage"
                                            :pending="pendingPercentage"></doughnut-chart>
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div id='dashboard_third_row' class="row margin-fix">
                <div class="col s12 m12 l4 xl4 col-right-padding">
                    <div class="card cyan lighten-1">
                        <div class="card-content">
                            <div class="white-text">
                                <h5>{{ trans('lang.this_Month') }}</h5>
                                <h6>{{ trans('lang.total_booking') }}</h6>
                                <h3>{{curMonthTotBooking}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l4 xl4 col-right-padding">
                    <div class="card deep-purple accent-2">
                        <div class="card-content">
                            <div class="white-text">
                                <h5>{{ trans('lang.last_month') }}</h5>
                                <h6>{{ trans('lang.total_booking') }}</h6>
                                <h3>{{lastMonthTotBooking}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 m12 l4 xl4">
                    <div class="card blue accent-3">
                        <div class="card-content">
                            <div class="white-text">
                                <h5>{{ trans('lang.till_now') }}</h5>
                                <h6>{{ trans('lang.total_booking') }}</h6>
                                <h3>{{allTimeTotBooking}}</h3>
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
    import lineChart from '../charts/lineChart'
    import doughnutChart from '../charts/doughnutChart'

    export default {
        extends: axiosGetPost,
        data() {
            return {
                item: [0],
                showChartLoader: true,
                totalAllBooking: 0,
                totalBooking: 0,
                todaysBooking: 0,
                todaysBookingPending: 0,
                curMonthTotBooking: 0,
                lastMonthTotBooking: 0,
                allTimeTotBooking: 0,
                hideChartLoader: true,
                lineChartItem: [],
                confirmedPercentage: 0,
                pendingPercentage: 0,
            }
        },
        components:
            {
                lineChart,
                doughnutChart
            },
        created() {
            this.getDashboardData('/dashboarddata');
        },
        methods:
            {
                getDashboardData(route) {
                    let instance = this;
                    this.axiosGet(route,
                        function (response) {

                            //line chart data
                            instance.lineChartItem = response.data.lineChartData;

                            //doughnut chart data
                            let confirm = response.data.doughnutChartData.confirmedCount;
                            let pending = response.data.doughnutChartData.pendingCount;
                            instance.confirmedPercentage = Math.round((((confirm) * 100) / (confirm + pending)));
                            instance.pendingPercentage = Math.round(((pending) * 100) / (confirm + pending));

                            //get other data for dashboard
                            instance.totalAllBooking = response.data.totalAllBooking;
                            instance.totalBooking = response.data.totalBooking;
                            instance.todaysBooking = response.data.todaysBooking;
                            instance.todaysBookingPending = response.data.todaysBookingPending;
                            instance.curMonthTotBooking = response.data.curMonthTotBooking;
                            instance.lastMonthTotBooking = response.data.lastMonthTotBooking;
                            instance.allTimeTotBooking = response.data.allTimeTotBooking;
                            instance.hideChartLoader = false;
                        },
                        function (response) {
                        },
                    );

                },
            }
    }
</script>
