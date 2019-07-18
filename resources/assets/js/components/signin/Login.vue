<template>
    <div class="back-img row margin-fix">
        <div class="col s12 m6 offset-m6 l4 offset-l8 no-padding">
            <div class="sign-in-sign-up-content valign-wrapper">
                <form class="col s12 sign-in-sign-up-form">
                    <div class="row margin-fix">
                        <div class="col s12">
                            <h5 class="center-align bluish-text">
                                {{ trans('lang.login') }}
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12" :class="{ 'has-error': errors.email }">
                            <input id="email" v-model="email" type="email" name="email" class="validate" :class="{'invalid': errors.email}">
                            <label for="email" :class="{'active': errors.email || email.length>0 || checkFocus('#email')}"> {{ trans('lang.login_email') }}</label>
                            <span class="helper-text" :data-error="getErrorMessage('email')"></span>
                        </div>
                        <div class="input-field col s12" :class="{ 'has-error': errors.password }">
                            <input id="password" v-model="password" type="password" class="validate" :class="{'invalid': errors.password}">
                            <label for="password" :data-error="getErrorMessage('password')" :class="{'active': errors.password || password.length>0 || checkFocus('#password')}">{{ trans('lang.login_password') }}</label>

                            <bar-pre-loader :loaderType="preLoaderType" v-if="hidePreLoader" :class="{'preloader-login': hidePreLoader}"></bar-pre-loader>
                            <span class="helper-text" :data-error="getErrorMessage('password')"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m12 l12">
                            <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="loginPost()">
                                <span > {{ trans('lang.login') }} </span>
                            </button>
                        </div>
                    </div>
                    <!--<div class="row">
                        <div class="col s12 m12 l12">
                            <span>{{ trans('lang.dont_have_account') }}</span> &nbsp <a href="/register" class="bluish-text">{{ trans('lang.sign_up') }}</a>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="col s12 m12 l12">
                            <a href="#" @click="passwordReset" class="bluish-text">{{ trans('lang.forgot_password') }}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m12 l12">
                            <a href="#" @click="redirectHome" class="bluish-text">{{ trans('lang.back_to_home') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data(){
            return{
                email:'',
                password:'',
                remember:true,
                preLoaderType:'load',
                hidePreLoader:false,
                errors: [],
                isActive:'active',
                getErrorMessage:function(field){
                    if(this.errors[field]){
                        if(this.errors[field][0]=='auth.failed')
                        {
                            return this.trans('lang.failed');
                        }
                        else
                        {
                            return this.errors[field][0];
                        }
                    }
                    else return "";
                }
            }
        },
        created(){
        },

        methods:{
            checkFocus(id){
                return $(id).is(":focus")
            },
            setPreloader(value)
            {
                this.hidePreLoader = value;
            },
            loginPost(){
                let instance= this;
                this.setPreloader(true);
                this.errors = [];
                instance.axiosPost('/login',{
                        email: this.email,
                        password: this.password
                    },
                    function(response){
                        instance.redirect('/');
                        setTimeout(function () {
                            instance.setPreloader(false);
                        },4000);
                    },
                    function (error) {
                        instance.setPreloader(false)
                        instance.errors = error.data.errors

                    });
            },
            passwordReset(){
                let instance=this;
                instance.redirect('/password/reset');
            },
            redirectHome(){
                let instance=this;
                instance.redirect('/');
            }
        },
        mounted(){

        }
    }
</script>