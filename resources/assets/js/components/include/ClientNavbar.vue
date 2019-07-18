<template>
    <div class="navbar-fixed">
        <nav>
            <div class="nav-wrapper">
                <a href="#" @click="dashboard" class="brand-logo brand-logo-hide"> <img :src="publicPath+'/uploads/logo/'+appLogo" alt="" class="logo"></a>
                <a href="#" data-target="mobile-demo" class="sidenav-trigger"><i class="la la-bars la-2x bluish-text"></i></a>
                <ul class="left client-new-booking">
                    <li>
                        <a href="#" @click="newBooking" class="bluish-text"><i class="la la-plus-circle la-2x bluish-text material-icons"></i>
                            {{ trans('lang.new_booking')}}
                        </a>
                    </li>
                </ul>
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
                        <a href="#" @click="newBooking"><i class="la la-plus-circle la-2x"></i><div>{{ trans('lang.new_booking') }}</div></a>
                    </li>
                    <li>
                        <a href="#" @click="dashboard"><i class="la la-desktop la-2x"></i><div>{{ trans('lang.dashboard') }}</div></a>
                    </li>
                    <li>
                        <a href="#" @click="bookingsclient"><i class="la la-user la-2x"></i><div>{{ trans('lang.bookings') }}</div></a>
                    </li>
                    <li>
                        <a href="#" @click="myprofile"><i class="la la-user la-2x"></i><div>{{ trans('lang.my_profile') }}</div></a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Dropdown Structure -->
        <div id='profile-dropdown' class='dropdown-content'>
            <!--<i class="material-icons ticker">arrow_drop_up</i>-->
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
                            <a href="#" @click="myprofile"><i class="la la-user la-2x"></i> {{ trans('lang.profile_title') }}</a>
                        </li>
                        <li>
                            <a href="#" @click="logout"><i class="la la-sign-out la-2x"></i> {{ trans('lang.logout_nv') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div id='notification-dropdown' class='dropdown-content'>
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
                            <a href='#' @click.prevent="upNofify(item.id)" class="notification-view" :class="{'unread-notification':  item.read_by.indexOf(profile.id)==-1}">
                                <div class="notification-view-parent">
                                    <div class="notification-view-child">
                                        <img :src="publicPath+'/uploads/profile/'+item.avatar" alt="" class="circle notification-icon">
                                    </div>
                                    <div class="notification-view-child">
                                        <p class="truncate margin-fix">
                                            {{ item.event }}
                                        </p>
                                        <small>{{localTime(item.created_at)}}</small>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <div class="center-align black-text" v-if="!hideCircleLoader && items.length == 0">{{ trans('lang.you_have_no_notifications')}}</div>
                    </ul>
                </div>
                <div class="dropdown-footer"><a href="#" @click.prevent="allNotification()" class="center-align bluish-text">{{ trans('lang.view_all') }}</a></div>
            </div>
        </div>
    </div>

</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        props: ["profile"],
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
            this.readNotification('/notify');
        },
        mounted()
        {
            $('.sidenav').sidenav();
            $('.profile-dropdown-trigger').dropdown();
            $('.notification-dropdown-trigger').dropdown();
        },

        methods:
            {
                readNotification(route)
                {
                    let instance =this;
                    instance.axiosGet(route,
                        function(response)
                        {
                            instance.items = response.data.notify;
                            instance.count = response.data.count;
                            setTimeout(function () {
                                instance.hideCircleLoader=false;
                            })
                        },
                    );
                },

                localTime(date_time)
                {
                    return moment(date_time + ' Z', 'YYYY-MM-DD HH:mm:ss Z', true).format(this.date_time);
                },

                upNofify(id)
                {
                    this.axiosPost('/upnotify/'+id,
                        {
                            read_by: this.profile.id
                        },
                    );
                    this.redirect("/booking/"+id);
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
                allNotification()
                {
                    this.redirect('/notification');
                },
                newBooking(){
                    let instance=this;
                    instance.redirect('/');
                },
                dashboard(){
                    let instance=this;
                    instance.redirect('/dashboard');
                },
                bookingsclient(){
                    let instance=this;
                    instance.redirect('/bookingsclient');
                },
                myprofile(){
                    let instance=this;
                    instance.redirect('/myprofile');
                },
                logout(){
                    let instance=this;
                    instance.redirect('/logout');
                },
            }
    }
</script>