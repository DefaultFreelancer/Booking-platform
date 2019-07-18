<template>
    <div class="back-img row margin-fix">
        <div class="col s12 m6 offset-m6 l4 offset-l8 no-padding">
            <div class="content-parent">
                <div class="content-child">
                    <form class="col s12">
                        <div class="row margin-fix">
                            <div class="col s12">
                                <h5 class="center-align bluish-text">
                                    {{ trans('lang.reset_password') }}
                                </h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12" :class="{ 'has-error': errors.email }">
                                <input id="email" v-model="email" type="email" class="validate" :class="{'invalid': errors.email}" readonly>
                                <label for="email" :class="{'active': errors.email || email.length>0 || checkFocus('#email')}">{{ trans('lang.login_email') }}</label>
                                <span class="helper-text" :data-error="getErrorMessage('email')"></span>
                            </div>

                            <div class="input-field col s12" :class="{ 'has-error': errors.password }">
                                <input id="password" v-model="password" type="password" class="validate" :class="{'invalid': errors.password}">
                                <label for="password"  :class="{'active': errors.password || password.length>0 || checkFocus('#password')}">{{ trans('lang.login_password') }}</label>
                                <span class="helper-text" :data-error="getErrorMessage('password')"></span>
                            </div>
                            <div class="input-field col s12" :class="{ 'has-error': errors.password }">
                                <input id="conf-password" v-model="password_confirmation" type="password" class="validate" :class="{'invalid': errors.password_confirmation}">
                                <label for="conf-password" :class="{'active': errors.password_confirmation || password_confirmation.length>0 || checkFocus('#conf-password')}">{{ trans('lang.confirm_password') }}</label>
                                <bar-pre-loader :loaderType="preLoaderType" v-if="hidePreLoader" class="preloader-login"></bar-pre-loader>
                                <span class="helper-text" :data-error="getErrorMessage('password_confirmation')"></span>

                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit"
                                        @click.prevent="changePassword()">{{ trans('lang.save') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props: ["token","emailreset"],
        data() {
            return {
                email: '',
                password: '',
                password_confirmation: '',
                not_match: false,
                errors: [],
                isSubmitted:false,
                preLoaderType:'load',
                hidePreLoader:false,
                getErrorMessage:function(field){
                    if(this.errors[field]){
                        return this.errors[field][0];
                    }
                    else return "";
                }
            }
        },
        mounted(){

            if(this.emailreset){
                this.email = this.emailreset;
            } else {
                $('#email').removeAttr('readonly');
            }
        },

        methods: {
            setPreloader(value)
            {
                this.hidePreLoader = value;
            },
            checkFocus(id){
                return $(id).is(":focus")
            },
            changePassword() {
                this.errors = [];
                let instance = this;
                this.setPreloader(true);
                instance.axiosPost('/password/reset',{
                        token: this.token,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation,
                    },
                    function(response){
                        instance.redirect("/dashboard");
                        instance.setPreloader(false);
                    },
                    function (error) {
                        instance.setPreloader(false);
                        instance.errors = error.data.errors;

                    }
                );
                this.isSubmitted = true;
                if (this.isValid) {

                    if (this.password == this.password_confirmation) {

                    }
                    else {
                        this.not_match = true;
                    }
                }
            }
        }
    }
</script>