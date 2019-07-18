<template>
    <div class="side-bar">
        <ul>
            <li class="side-bar-logo">
                <a href="#" @click="dashboardclick">
                    <img :src="publicPath+'/uploads/logo/'+appLogo" alt="">
                </a>
            </li>
            <li :class="{'active-side-bar': route == 'dashboard' }">
                <a href="#" @click="dashboardclick">
                    <i class="la la-desktop la-3x"></i>
                    <div>{{ trans('lang.dashboard') }}</div>
                </a>
            </li>
            <li v-if="services==1" :class="{'active-side-bar': route == 'services' }">
                <a href="#" @click="servicesclick">
                    <i class="la la-globe"></i>
                    <div>{{ trans('lang.services') }}</div>
                </a>
            </li>
            <li v-if="booking==1" :class="{'active-side-bar': route == 'bookings' || routeurl.search('booking-details')>-1 }">
                <a href="#" @click="bookingsclick">
                    <i class="la la-credit-card"></i>
                   <div> {{ trans('lang.bookings') }}</div>
                </a>
            </li>
            <li v-if="clientmanage==1" :class="{'active-side-bar': route == 'clients' || routeurl.search('client')>-1 }">
                <a href="#" @click="clientsclick">
                    <i class="la la-users"></i>
                    <div> {{ trans('lang.clients') }}</div>
                </a>
            </li>
            <li v-if="reportpermission == 1" :class="{'active-side-bar': route == 'reports' }">
                <a href="#" @click="reportsclick">
                    <i class="la la-pie-chart"></i>
                   <div> {{ trans('lang.reports') }}</div>
                </a>
            </li>
            <li v-if="userroles==1 || usermanage==1 || appsettings==1 || emailsettings == 1 || offdaysettings == 1 || holidaysettings == 1 || emailtemplate == 1 || clientsetting == 1" :class="{'active-side-bar': route == 'settings' || routeurl.search('/user')>-1}">
                <a href="#" @click="settingsclick">
                    <i class="la la-cogs la-3x"></i>
                    <div>{{ trans('lang.settings') }}</div>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['route','routeurl','services','booking','userroles','usermanage','appsettings','emailsettings','offdaysettings','holidaysettings','emailtemplate', 'clientmanage', 'reportpermission', 'clientsetting'],

        data(){
            return{}
        },
        mounted(){

        },
        methods: {
            dashboardclick(){
                let instance=this;
                instance.redirect('/dashboard');
            },
            servicesclick(){

                let instance=this;
                instance.redirect('/services');
            },
            bookingsclick(){
                let instance=this;
                instance.redirect('/bookings');
            },
            clientsclick(){
                let instance=this;
                instance.redirect('/clients');
            },
            reportsclick(){
                let instance=this;
                instance.redirect('/reports');
            },
            settingsclick(){
                let instance=this;
                instance.redirect('/settings');
            },
        }
    }
</script>