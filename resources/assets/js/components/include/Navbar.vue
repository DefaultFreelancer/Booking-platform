<template>
    <header>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" @click="dashboardclick" class="brand-logo brand-logo-hide"> <img :src="publicPath+'/uploads/logo/'+appLogo" alt="" class="logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="la la-bars la-2x bluish-text"></i></a>
                <ul class="right">
                    <li>
                        <a href="#" @click.prevent="upCount(profile.id)" class="notification-dropdown-trigger" data-target='notification-dropdown'>
                            <i class="la la-bell la-2x bluish-text material-icons"></i>
                            <div class="notification-badge" v-if="count!=0">{{ count }}</div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="profile-dropdown-trigger img-link" data-target='profile-dropdown'>
                            <img :src="publicPath+'/uploads/profile/'+profile.avatar" alt="" class="circle avatar">
                        </a>
                    </li>
                </ul>
                <ul class="sidenav" id="mobile-demo">
                    <li>
                        <a href="#" @click="dashboardclick">
                            <i class="la la-desktop la-2x"></i>
                            <div>{{ trans('lang.dashboard') }}</div>
                        </a>
                    </li>
                    <li v-if="services == 1">
                        <a href="#" @click="servicesclick">
                            <i class="la la-globe la-2x"></i>
                            <div>{{ trans('lang.services') }}</div>
                        </a>
                    </li>
                    <li v-if="booking == 1">
                        <a href="#" @click="bookingsclick">
                            <i class="la la-credit-card la-2x"></i>
                            <div> {{ trans('lang.bookings') }}</div>
                        </a>
                    </li>
                    <li v-if="clientmanage == 1">
                        <a href="#" @click="clientsclick">
                            <i class="la la-users la-2x"></i>
                            <div> {{ trans('lang.clients') }}</div>
                        </a>
                    </li>
                    <li v-if="reportpermission == 1">
                        <a href="#" @click="reportsclick">
                            <i class="la la-pie-chart la-2x"></i>
                            <div> {{ trans('lang.reports') }}</div>
                        </a>
                    </li>
                    <li v-if="userroles==1 || usermanage==1 || appsettings==1 || emailsettings == 1 || offdaysettings == 1 || holidaysettings == 1 || emailtemplate == 1 || clientsetting == 1">
                        <a href="#" @click="settingclick">
                            <i class="la la-cogs la-2x"></i>
                            <div>{{ trans('lang.settings') }}</div>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dropdown Structure -->
        <div id='profile-dropdown' class='dropdown-content'>
            <div class="ticker"></div>
            <div class="dropdown-wrapper">
                <div class="row margin-fix">
                    <ul>
                        <li class="user-name-pic">
                            <img :src="publicPath+'/uploads/profile/'+profile.avatar" alt="" class="circle avatar-large">
                            <div class="user-name">
                                <div>{{profile.first_name+' '+profile.last_name }}</div>
                            </div>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="#" @click="myprofileclick">
                                <i class="la la-user la-2x"></i> {{ trans('lang.profile_title') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" @click="logoutclick">
                                <i class="la la-sign-out la-2x"></i> {{ trans('lang.logout_nv') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id='notification-dropdown' class='dropdown-content'>
            <!--<i class="material-icons ticker">arrow_drop_up</i>-->
            <div class="ticker"></div>
            <div class="dropdown-wrapper">
                <div class="dropdown-header"><span class="bluish-text">{{ trans('lang.your_notifications') }}</span></div>
                <div class="row margin-fix" v-if="hideCircleLoader">
                    <div class="col s4 offset-s4 center-align">
                        <div class="notification-loader">
                            <circle-loader></circle-loader>
                        </div>
                    </div>
                </div>
                <div>
                    <ul class="notifications">
                        <li v-for="item in items">
                            <a href='#' @click.prevent="upNofify(item.id,item.booking_id)" class="notification-view" :class="{'unread-notification':  item.read_by.indexOf(profile.id)==-1}">
                                <div class="notification-view-parent">
                                    <div class="notification-view-child">
                                        <img :src="publicPath+'/uploads/profile/'+item.avatar" alt="" class="circle notification-icon">
                                    </div>
                                    <div class="notification-view-child">
                                        <p class="margin-fix">
                                            {{ item.first_name+" "+item.last_name+" "+trans('lang.'+item.event)+" #"+item.booking_id}}
                                        </p>
                                        <small>{{localTime(item.created_at)}}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <div class="center-align black-text" v-if="!hideCircleLoader && items.length == 0">{{ trans('lang.you_have_no_notifications')}}</div>
                    </ul>
                </div>
                <div class="dropdown-footer"><a href="#" @click.prevent="allNoti()" class="center-align bluish-text">{{ trans('lang.view_all') }}</a></div>
            </div>
        </div>
    </div>
</header>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        props: ["profile",'services','booking','userroles','usermanage','appsettings','emailsettings','offdaysettings','holidaysettings','emailtemplate', 'clientmanage', 'reportpermission', 'clientsetting'],
        extends: axiosGetPost,
        data() {
            return {
                first_name: '',
                last_name: '',
                avatar: '',
                id:'',
                read_by:[],
                items: [],
                date_time:'',
                count: 0,
                hideCircleLoader:true,
                users:[],
            }
        },
        created()
        {
            this.readNotifi('/notify');
        },
        mounted(){

            $('.sidenav').sidenav();
           $('.profile-dropdown-trigger').dropdown();
           $('.notification-dropdown-trigger').dropdown();
        },

        methods: {
            readNotifi(route)
            {
                let instance =this;
                instance.axiosGet(route,
                    function(response)
                    {
                        instance.count = response.data.count;
                        instance.items = response.data.notify;
                        setTimeout(function () {
                            instance.hideCircleLoader=false;
                        })
                    },
                );
            },
            upNofify(id,booking_id)
            {
                this.axiosPost('/upnotify/'+id,
                    {
                        read_by: this.profile.id
                    },
                );
                this.redirect("/booking-details/"+booking_id);
            },
            upCount(id)
            {
                this.axiosPost('/countup/'+id,
                    {
                        read_by: this.profile.id
                    },
                );
                this.count =0;
            },
            localTime(date_time)
            {
                return moment(date_time + ' Z', 'YYYY-MM-DD HH:mm:ss Z', true).format(this.date_time);
            },
            allNoti()
            {
                let instance =this;
                instance.redirect("/notification");
            },
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
            settingclick(){
                let instance=this;
                instance.redirect('/settings');
            },
            myprofileclick(){
                let instance=this;
                instance.redirect('/myprofile');
            },
            logoutclick(){
                let instance=this;
                instance.redirect('/logout');
            },

        }
    }
</script>