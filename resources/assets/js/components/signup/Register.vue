<template>
    <div class="back-img row margin-fix">
        <div class="col s12 m6 offset-m6 l4 offset-l8 no-padding">
            <div class="sign-in-sign-up-content valign-wrapper">
                <form class="col s12 sign-in-sign-up-form">
                    <div class="row margin-fix">
                        <div class="col s12">
                            <h5 class="center-align bluish-text">
                                {{ trans('lang.sign_up') }}
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12" :class="{ 'has-error': errors.first_name }">
                            <input id="first_name" v-model="first_name" type="text" name="first_name" class="validate" :class="{'invalid': errors.first_name}">
                            <label for="first_name"  :class="{'active': errors.first_name || first_name.length>0 || checkFocus('#first_name')}">{{ trans('lang.first_name') }}</label>
                            <span class="helper-text" :data-error="getErrorMessage('first_name')"></span>
                        </div>

                        <div class="input-field col s12" :class="{ 'has-error': errors.last_name }">
                            <input id="last_name" v-model="last_name" type="text" name="last_name" class="validate" :class="{'invalid': errors.last_name}">
                            <label for="last_name"  :class="{'active': errors.last_name || last_name.length>0 || checkFocus('#last_name')}">{{ trans('lang.last_name') }}</label>
                            <span class="helper-text" :data-error="getErrorMessage('last_name')"></span>
                        </div>

                        <div class="input-field col s12" :class="{ 'has-error': errors.email }">
                            <input id="email" v-model="email" type="email" name="email" class="validate" :class="{'invalid': errors.email}" readonly>
                            <label for="email"  :class="{'active': errors.email || email.length>0 || checkFocus('#email')}">{{ trans('lang.login_email') }}</label>
                            <span class="helper-text" :data-error="getErrorMessage('email')"></span>
                        </div>

                        <div class="input-field col s12" :class="{ 'has-error': errors.password }">
                            <input id="password" v-model="password" type="password" class="validate" :class="{'invalid': errors.password}">
                            <label for="password"  :class="{'active': errors.password || password.length>0 || checkFocus('#password')}">{{ trans('lang.login_password') }}</label>
                            <span class="helper-text" :data-error="getErrorMessage('password')"></span>
                        </div>

                        <div class="input-field col s12" :class="{ 'has-error': errors.password_confirmation }">
                            <input id="conf-password" v-model="password_confirmation" type="password" class="validate" :class="{'invalid': errors.password_confirmation}">
                            <label for="conf-password" :class="{'active': errors.password_confirmation || password_confirmation.length>0 || checkFocus('#conf-password')}">{{ trans('lang.confirm_password') }}</label>
                            <bar-pre-loader :loaderType="preLoaderType" v-if="hidePreLoader" :class="{'preloader-login': hidePreLoader}"></bar-pre-loader>
                            <span class="helper-text" :data-error="getErrorMessage('password_confirmation')"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12">
                            <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit"
                                    @click.prevent="registersPost() , showPreloader='show'">{{ trans('lang.sign_up') }}
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                            <span>{{ trans('lang.already_have_an_account?')}} </span>
                            <a href="#" @click="login" class="bluish-text">{{ trans('lang.login') }}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m12 l12">
                            <a href="#" @click="homePage" class="bluish-text">{{ trans('lang.back_to_home') }}</a>
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
        props:['emailadd','token'],

            data() {
            return {
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                password_confirmation: '',
                preLoaderType:'load',
                hidePreLoader:false,
                errors: [],
                getErrorMessage:function(field){
                    if(this.errors[field]){
                        return this.errors[field][0];
                    }
                    else
                    {
                        return "";
                    }
                }
            }
        },

        mounted(){
            if(this.emailadd){
                this.email = this.emailadd;
            } else {
                $('#email').removeAttr('readonly');
            }

        },

        methods: {
            checkFocus(id){
                return $(id).is(":focus")
            },
            setPreloader(value)
            {
                this.hidePreLoader = value;
            },
            registersPost() {
                this.errors = [];
                var instance = this;
                if(instance.emailadd && instance.token){
                    this.setPreloader(true);
                    instance.axiosPost('/register/'+instance.token,{
                            first_name: this.first_name,
                            last_name: this.last_name,
                            email: this.email,
                            password: this.password,
                            password_confirmation: this.password_confirmation,
                        },
                        function(response){
                            M.toast({html:instance.trans('lang.registration_done')});
                            instance.login();
                            instance.setPreloader(false);
                        },
                        function (error) {
                            instance.errors = error.data.errors
                            instance.setPreloader(false);

                        });

                } else {
                    this.setPreloader(true);
                    instance.axiosPost('/registerclient',{
                        first_name: this.first_name,
                        last_name: this.last_name,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    },
                    function(response){
                        if(response.data.message)
                        {
                            M.toast({html:response.data.message})
                        }
                        else
                        {
                            M.toast({html:instance.trans('lang.confirmation_email_send')});
                        }
                       instance.login();
                        instance.setPreloader(false);
                    },
                    function (error) {
                        M.toast({html:instance.trans('lang.something_wrong'),classes:'red lighten-1'});
                        instance.setPreloader(false);

                    });
                }
                
            },
            login(){
                let instance=this;
                instance.redirect('/login');
            },
            homePage(){
                let instance=this;
                instance.redirect('/');
            }
        },
    }
</script>