<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin">{{ trans('lang.invite_user') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setShowInvite(false)"><i
                        class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <div class="row">
                <div class="input-field col s12">
                    <input id="email" v-model="email" type="email" class='validate'
                           :class="{'invalid': errorInvalid.email}">
                    <label for="email"  :class="{'active': error}">{{trans('lang.login_email') }}</label>
                    <span class="helper-text" :data-error="errorMessage.email"></span>
                </div>
                <div class="input-field col s12">
                    <select v-model="invited_as" id="invited_as">
                        <option value="" disabled selected>{{ trans('lang.choose_one') }}</option>
                        <option v-for="role in roles" :value="role.id"> {{role.title}}</option>
                    </select>
                    <label>{{ trans('lang.role') }}</label>
                    <span v-if="errorInvalid.invited_as" class="red-text helper-text">{{ errorMessage.invited_as}}</span>
                </div>

                <div class="input-field col s12" :class="{'error-m': errorInvalid.invited_as}">
                    <button class="btn waves-effect waves-light bluish-back mob-btn"
                            @click.prevent="is_disable(),inviteUser()" type="submit" :disabled="is_disabled">{{
                        trans('lang.invite_button') }}
                    </button>
                    <button class="btn cancel-btn waves-effect waves-grey mob-btn modal-close"
                            @click.prevent="setShowInvite(false), setActive">{{ trans('lang.cancel') }}
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
        data() {
            return {
                email: '',
                invited_as: '',
                roles: [],
                error: false,
                errorMessage: {
                    email: this.trans('lang.required_input_field'),
                    invited_as: this.trans('lang.please_choose_one'),
                },
                errorInvalid: {
                    email: false,
                    invited_as: false,
                },
                is_disabled: false,
                preloaderType: 'load',
                hidePreloader: '',
            }
        },
        mounted() {
            $('select').formSelect();
            this.getRoleId();
            let instance = this;
            $('#email').on('input', function () {
                instance.errorInvalid.email = false;
                instance.errorMessage.email = instance.trans('lang.invalid_email')
            });
        },
        methods: {
            setActive: function () {
                this.$emit('setActive', 1);
                $('#user-invite-modal').modal();
            },
            setErrorInvalid() {
                this.error = false;
                this.errorInvalid.email = false
                this.errorInvalid.invited_as = false;
            },
            checkFocus(id) {
                return $(id).is(":focus")
            },
            setPreloader: function (type, action) {
                //this.$emit('setPreloader', e,f);
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            setShowInvite: function (e) {
                this.$emit('setShowInvite', e);
                this.setPreloader('load', 'hide');
            },
            inviteUser() {
                let instance = this;
                if (instance.invited_as == '') {
                    this.$nextTick(function () {
                        instance.errorInvalid.invited_as = true;
                    })
                    instance.is_disabled = false;
                }
                var emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (!this.email.match(emailFormat)) {
                    if (this.email == '') {
                        instance.errorMessage.email = instance.trans('lang.required_input_field')
                    }
                    else {
                        instance.errorMessage.email = instance.trans('lang.invalid_email');
                    }
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.email = true;
                    })
                    instance.is_disabled = false;
                }
                if (this.email.match(emailFormat) && instance.invited_as != '') {
                    this.setPreloader('load', '');
                    instance.error = true;
                    instance.axiosPost('/invite', {
                            email: this.email,
                            invited_as: this.invited_as,
                        },
                        function (response) {
                            instance.getRoleId();
                            instance.setPreloader('load', 'hide');
                            M.toast({html: instance.trans('lang.invitation_sent_to') + instance.email});
                            instance.email = '';
                            instance.invited_as = '';
                            $(document).ready(function () {
                                M.updateTextFields();
                                $('select').formSelect();
                            });
                            instance.is_disabled = false;
                            instance.setShowInvite(false);
                            $('#user-invite-modal').modal('close');
                        },
                        function (error) {
                            instance.setPreloader('load', 'hide');
                            instance.error = true;
                            instance.errors = error.data.errors
                            M.toast({html: instance.trans('lang.invitation_error'), classes: 'red lighten-1'});
                            instance.is_disabled = false;
                            $('#user-invite-modal').modal('close');

                        }
                    );
                }
            },
            getRoleId() {
                this.setPreloader('load', '');
                let instance = this;
                this.axiosGet('/allroleid',
                    function (response) {
                        instance.roles = response.data;
                        setTimeout(function () {
                            $('select').formSelect();
                            $('select').on('change', function () {
                                instance.errorInvalid.invited_as = false;
                            });
                            instance.setPreloader('load', 'hide');
                        })
                    },
                    function (response) {
                        instance.setPreloader('load', 'hide');
                    },
                );
            },
            is_disable() {
                this.is_disabled = true;
            }
        }
    }
</script>