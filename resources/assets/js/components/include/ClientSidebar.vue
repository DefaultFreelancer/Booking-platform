<template>
    <div class="side-bar">
        <ul>
            <li class="side-bar-logo">
                <a href="#" @click="dashboard">
                    <img :src="publicPath+'/uploads/logo/'+appLogo" alt="">
                </a>
            </li>
            <li :class="{'active-side-bar': route == '/dashboard'}">
                <a href="#" @click="dashboard">
                    <i class="la la-desktop la-3x"></i>
                    <div>{{ trans('lang.dashboard') }}</div>
                </a>
            </li>
            <li :class="{'active-side-bar': route.search('/bookingsclient')>-1 }">
                <a href="#" @click="bookingsclient">
                    <i class="la la-credit-card la-3x"></i>
                    <div>{{ trans('lang.booking') }}</div>
                </a>
            </li>
            <li :class="{'active-side-bar': route == '/myprofile'}">
                <a href="#" @click="myprofile">
                    <i class="la la-user la-3x"></i>
                    <div>{{ trans('lang.my_profile') }}</div>
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default{
        extends: axiosGetPost,
        props:['route'],
        data(){
            return{}
        },
        methods:{
            bookingsclient(){
                let instance = this;
                instance.redirect("/bookingsclient");
            },
            myprofile(){
                let instance = this;
                instance.redirect("/myprofile");
            },
            dashboard(){
                let instance =this;
                instance.redirect("/dashboard");
            }
        }
    }
</script>