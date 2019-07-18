<template>
    <form class="col s12" role="form">
        <div class="row">
            <div class="col s12">
                <h5 class="center-align bluish-text">
                    {{ trans('lang.forgot_password') }}
                </h5>
                <h6 class="center-align">
                    {{ trans('lang.enter_email_address') }}
                </h6>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12" :class="{ 'has-error': errors.email }">
                <input id="email" v-model="email" type="email" name="email" class="validate" :class="{'invalid': errors.email}">
                <label for="email"  :class="{'active': errors.email || email.length>0 || checkFocus('#email')}"> {{ trans('lang.login_email') }}</label>
                <bar-pre-loader :loaderType="preLoaderType" v-if="hidePreLoader" class="preloader-login"></bar-pre-loader>
                <span class="helper-text" :data-error="getErrorMessage('email')"></span>
            </div>
            <div class="input-field col s12 m12 l12">
                <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="passRecover()">{{
                    trans('lang.send') }}
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col s12 m12 l12">
                <a href="#" @click="login" class="bluish-text">{{ trans('lang.back_to') + ' ' + trans('lang.login') }}</a>
            </div>
        </div>
        <!--<div class="row">
            <div class="col s12 m12 l12">
                <span>{{ trans('lang.dont_have_account') }}</span> &nbsp <a href="/register" class="bluish-text">{{
                trans('lang.sign_up') }}</a>
            </div>
        </div>-->
    </form>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data() {
            return {
                email: '',
                not_found: false,
                isSubmitted: false,
                errors : [],
                preLoaderType:'load',
                hidePreLoader:false,
                getErrorMessage:function(field){
                    if(this.errors[field]){
                        if(Array.isArray(this.errors[field]))
                        {
                            return this.errors[field][0];
                        }
                        else
                        {
                            return this.errors[field];
                        }

                    }
                    else return "";
                }
            }
        },
        computed: {
            validation: function () {
                return {
                    email: !!this.email.trim(),
                };
            },
            isValid: function () {
                let validation = this.validation
                return Object.keys(validation).every(function (key) {
                    return validation[key]

                })
            },
        },
        methods: {
            setPreloader(value)
            {
                this.hidePreLoader = value;
            },
            login(){
                let instance=this;
                instance.redirect('/login');
            },
            checkFocus(id){
                return $(id).is(":focus")
                this.errors = [];
            },
            passRecover() {
                this.errors = [];
                let instance = this;
                this.setPreloader(true);
                instance.axiosPost('/recoverpass',{
                        email: this.email
                    },
                    function(response){
                        M.toast({html:instance.trans('lang.reset_email_send')});
                        instance.setPreloader(false);
                    },
                    function (error) {
                        instance.setPreloader(false);
                        instance.errors = error.data.errors
                    }
                );

            }
        }
    }
</script>