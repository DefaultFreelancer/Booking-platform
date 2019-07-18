<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.client_settings') }}</h5>
                </div>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="main-layout-card-content" v-show="hidePreloader=='hide'">
            <div class="row">
                <label for="client-setting-1">
                <input type="checkbox" id="client-setting-1" v-model="can_signup" @change="checkSelected(can_signup,false,false)"/>
                <span>{{ trans('lang.client_can_signup') }}</span></label>
            </div>
            <div v-if="can_signup" class="row">
                <label for="client-setting-3">
                <input type="checkbox" id="client-setting-3" v-model="can_login" @change="checkSelected(can_signup,can_login,false)"/>
                <span>{{ trans('lang.client_can_login') }}</span></label>
            </div>
            <div v-if="can_signup && can_login" class="row">
                <label for="client-setting-4">
                <input type="checkbox" id="client-setting-4" v-model="submit_booking_after_login" @change="checkSelected(can_signup,can_login,submit_booking_after_login)"/>
                <span>{{ trans('lang.client_can_submit_booking_when_login') }}</span></label>
            </div>
            <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="save()">{{ trans('lang.save') }}</button>
        </div>
    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default
    {
        extends: axiosGetPost,
        data(){
            return{
                preloaderType:'load',
                hidePreloader:'',
                can_signup: 0,
                can_login: 0,
                submit_booking_after_login: 0,
                settingValue: {}
            }
        },
        mounted(){
            this.getData();
        },
        methods: {
            checkSelected(can_signup,can_login,submit_booking_after_login){
              this.can_signup = can_signup;
              this.can_login = can_login;
              this.submit_booking_after_login = submit_booking_after_login;
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            getData(){
                let instance = this;
                instance.setPreloader('load','');
                this.axiosGet('/clientsetting',
                    function(response){
                        instance.settingValue = response.data,
                            instance.can_signup = instance.settingValue.can_signup,
                            instance.can_login = instance.settingValue.can_login,
                            instance.submit_booking_after_login = instance.settingValue.submit_booking_after_login
                        setTimeout(function () {
                            M.updateTextFields();
                            instance.setPreloader('load','hide');
                        })
                    },
                    function (response) {
                        instance.setPreloader('load','hide');
                    },
                );
            },
            save(){
                let instance = this;
                instance.setPreloader('load','');
                instance.axiosPost('/clientsetting',{
                        can_signup: instance.can_signup,
                        can_login: instance.can_login,
                        submit_booking_after_login: instance.submit_booking_after_login
                    },
                    function(response){
                        M.toast({html:instance.trans('lang.client_setting_saved_successfully')});
                        instance.setPreloader('load','hide');
                    },
                    function (error) {
                        instance.errors = error.data.errors;
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreloader('load','hide');

                    }
                );
            }
        }
    }
</script>