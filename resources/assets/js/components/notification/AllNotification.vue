<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.notifications') }}
        </div>
        <div class="admin-layout-margin">
            <div class="main-layout-card">
                <div class="main-layout-card-header">
                    <div class="main-layout-card-content-wrapper">
                        <div class="main-layout-card-header-contents">
                            <h5 class="bluish-text no-margin">{{ trans('lang.your_notifications') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="main-layout-card-content">
                    <ul class="all-notification">
                        <li @click.prevent="upNofify(notification.id,notification.booking_id)" class="all-notification-item read-all-notification" :class="{'unread-all-notification':  notification.read_by.indexOf(profile.id)==-1}" v-for="notification in notifications">
                            <div class="all-notification-view-parent">
                                <div class="all-notification-view-child">
                                    <img :src="publicPath+'/uploads/profile/'+notification.avatar" alt="" class="circle notification-icon">
                                </div>
                                <div class="all-notification-view-child notification-title">
                                    {{ notification.first_name+" "+notification.last_name+" "+trans('lang.'+notification.event)+" #"+notification.booking_id}}
                                    <br>
                                    <span class="received-date">{{ trans('lang.received_at') }} : {{ localTime(notification.created_at)}}</span>
                                </div>
                            </div>
                        </li>
                        <li class="bluish-text no-notification" v-if="notifications.length == 0">{{ trans('lang.you_have_no_notifications')}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['data_notify','auth_profile'],
        data(){
          return{
              date_time:this.dateTimeFormat,
              notifications:this.data_notify,
              profile:this.auth_profile,

          }
        },
        methods:{
            upNofify(id,booking_id){
                let instance = this;
                instance.axiosPost('/upnotify/'+id,{
                        read_by: this.profile.id
                    },
                    function(response){
                    },
                    function (error) {

                    });
                instance.redirect("/booking-details/"+booking_id);

            },
            localTime(date_time)
            {
                return moment(date_time + ' Z', 'YYYY-MM-DD HH:mm:ss Z', true).format(this.date_time);
            },
        },
    }
</script>