<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin" v-if="clientId">{{ trans('lang.edit') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setActive"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <div class="row">
                <div class="input-field col s12">
                    <input id="first_name" type="text" v-model="first_name" :class="{'invalid': errorInvalid.first_name}" @input="errorInvalid.first_name = false">
                    <label for="first_name"  :class="{'active': error}">
                        {{trans('lang.first_name') }}
                    </label>
                    <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="input-field col s12">
                    <input id="last_name" type="text" v-model="last_name" :class="{'invalid': errorInvalid.last_name}" @input="errorInvalid.last_name = false">
                    <label for="last_name" :class="{'active': error}">
                        {{ trans('lang.last_name') }}
                    </label>
                    <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="input-field col s12">
                    <input id="email" type="email" v-model="email" :class="{'invalid': errorInvalid.email}" @input="errorInvalid.email = false">
                    <label for="email" :class="{'active': error}">
                        {{trans('lang.login_email') }}
                    </label>
                    <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="input-field col s12">
                    <input id="phone" type="text" v-model="phone" :class="{'invalid': errorInvalid.phone}" @input="errorInvalid.phone = false">
                    <label for="phone" :class="{'active': error}">
                        {{trans('lang.phone') }}
                    </label>
                    <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="col s12">
                    <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="is_disable(),setErrorInvalid(),updateClient()":disabled="is_disabled">{{ trans('lang.save') }}
                    </button>
                    <button class="btn cancel-btn waves-effect waves-grey mob-btn modal-close" @click.prevent="setActive">{{ trans('lang.cancel') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props: ['clientId', 'id'],
        data() {
            return {
                user: [],
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                error: false,
                is_disabled: false,
                preloaderType:'',
                hidePreloader:'',
                errorInvalid: {
                    first_name: false,
                    last_name: false,
                    email: false,
                    phone: false,
                },
                emailError:'',
                isEmailValid: true,
            }
        },
        mounted() {
            let instance = this;
            instance.readData();
        },
        methods: {
            setErrorInvalid() {
                this.error = false;
                this.errorInvalid.first_name = false;
                this.errorInvalid.last_name = false;
                this.errorInvalid.email = false;
                this.errorInvalid.phone = false;
            },
            setError(value) {
                let instance = this;
                instance.$nextTick(function () {
                    instance.error = true;
                    instance.errorInvalid[value] = true;
                });
                instance.setPreloader('load', 'hide');
            },
            setActive: function () {
                this.$emit('setActive', false);
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            newData() {
                this.$emit('newData');
            },
            readData() {
                let instance = this;
                instance.setPreloader('load','');
                instance.axiosGet('/clients/' + this.clientId,
                    function(response){
                        instance.user = response.data;
                        instance.first_name = instance.user.first_name;
                        instance.last_name = instance.user.last_name;
                        instance.email = instance.user.email;
                        instance.phone = instance.user.phone;
                        instance.$nextTick(function(){
                            M.updateTextFields();
                        });
                        instance.setPreloader('load','hide');
                    },
                    function (response) {
                        instance.setPreloader('load','hide')
                    },
                );
            },
            updateClient() {
                let instance = this;
                // Validate all required field
                if (this.first_name === '') instance.setError('first_name');
                if (this.last_name === '') instance.setError('last_name');

                if (this.phone === '') instance.setError('phone');

                let emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if(!this.email.match(emailFormat))
                {
                    if(this.email === '')
                    {
                        instance.emailError = 'required_input_field';
                        instance.isEmailValid = false
                    }
                    else
                    {
                        instance.emailError = 'invalid_email';
                        instance.isEmailValid = false
                    }
                    instance.setError('email');
                }

                if (this.first_name && this.last_name && this.isEmailValid == true && this.phone) {
                    this.setPreloader('load','');
                    instance.axiosPost('/clients/' + this.clientId,{
                            first_name: this.first_name,
                            last_name: this.last_name,
                            email: this.email,
                            phone: this.phone,
                        },
                        function(response){
                            instance.$hub.$emit('reloadDataTable');
                            M.toast({html:instance.trans('lang.client_updated_successfully')});
                            instance.setActive();
                            instance.is_disabled = false;
                            $('#client-modal').modal('close');
                            instance.$nextTick(function(){
                                setTimeout(function () {
                                    M.updateTextFields();
                                })
                            });
                        },
                        function (error) {
                            instance.errors = error.data.errors;
                            instance.setPreloader('load','hide');
                            instance.is_disabled = false;

                        });
                    this.newData();
                }
                else
                {
                    instance.is_disabled = false;
                }
            },
            is_disable() {
                this.is_disabled = true;
            }
        }
    }
</script>