<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin" v-if="id">{{ trans('lang.edit_payment_method') }}</h5>
                <h5 class="bluish-text no-margin" v-else>{{ trans('lang.add_payment_method') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setActive"><i
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
                    <input id="title" type="text" v-model="title" :class="{'invalid': errorInvalid.title}">
                    <label for="title" :class="{'active': error}">{{
                        trans('lang.name') }}</label>
                    <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <div class="label-for-radio">
                                <label>{{ trans('lang.status') }}</label>
                            </div>
                        </div>
                        <label for="statusYes">
                            <input type="radio" id="statusYes" class="with-gap" v-model="enable" value="1"/>
                            <span> {{ trans('lang.enable') }}</span></label>
                        <label for="autoNo">
                            <input type="radio" id="autoNo" class="with-gap" v-model="enable" value="0"/>
                            <span>{{ trans('lang.disable') }}</span></label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="input-field">
                            <div class="label-for-radio">
                                <label>{{ trans('lang.available_to_client') }}</label><br>
                            </div>
                        </div>
                        <label for="availableYes">
                        <input type="radio" id="availableYes" class="with-gap" v-model="available_to_client" value="1"/>
                       <span>{{ trans('lang.yes') }}</span></label>
                        <label for="availableNo">
                        <input type="radio" id="availableNo" class="with-gap" v-model="available_to_client" value="0"/>
                        <span>{{ trans('lang.no') }}</span></label>
                    </div>
                </div>
                <div v-if="type=='stripe' || type =='paypal'">
                    <div class="input-field col s12" v-if="type=='stripe'">
                        <input id="publicKey" type="text" v-model="publickey"
                               :class="{'invalid': errorInvalid.publickey}" @input="errorInvalid.publickey =false">
                        <label for="publicKey"
                               :class="{'active': error}">{{ trans('lang.publishablekey') }}</label>
                        <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div class="input-field col s12" v-if="type=='paypal'">
                        <input id="clientId" type="text" v-model="clientId"
                               :class="{'invalid': errorInvalid.clientId}" @input="errorInvalid.clientId =false">
                        <label for="clientId"
                               :class="{'active': error}">{{ trans('lang.client_id') }}</label>
                        <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div class="input-field col s12">
                        <input id="secretKey" type="text" class="cloakInputFiled" v-model="secretkey"
                               :class="{'invalid': errorInvalid.secretkey}"
                               @input="errorInvalid.secretkey =false, changeType=true">
                        <label for="secretKey"
                               :class="{'active': error}">{{ trans('lang.secretkey') }}</label>
                        <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                    <div class="row" v-if="type=='paypal'">
                        <div class="col s12">
                            <div class="input-field">
                                <div class="label-for-radio">
                                    <label>{{ trans('lang.mode') }}</label><br>
                                </div>
                            </div>
                            <label for="modeLive">
                                <input type="radio" id="modeLive" class="with-gap" v-model="paypalMode" value="1"/>
                                <span>{{ trans('lang.live') }}</span></label>
                            <label for="modeSandbox">
                                <input type="radio" id="modeSandbox" class="with-gap" v-model="paypalMode" value="0"/>
                                <span>{{ trans('lang.sandbox') }}</span></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s12">
                    <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit"
                            @click.prevent="is_disable(),setErrorInvalid(),save()" :disabled="is_disabled">{{
                        trans('lang.save') }}
                    </button>
                    <button class="btn cancel-btn waves-effect waves-grey mob-btn modal-close"
                            @click.prevent="setActive">{{ trans('lang.cancel') }}
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
        props: ['id'],
        data() {
            return {
                item: [],
                title: '',
                type: 'custom',
                enable: 1,
                available_to_client: 0,
                secretkey: '',
                publickey: '',
                clientId:'',
                changeType: false,
                error: false,
                is_disabled: false,

                errorInvalid: {
                    title: false,
                    type: false,
                    enable: false,
                    secretkey: false,
                    publickey: false,
                    clientId:false,
                },
                hidePreloader: '',
                preloaderType: 'load',
                paypalMode:1,
            }
        },
        mounted() {
            let instance = this;
            if (this.id) {
                instance.readData();
            }
            else {
                this.setPreloader('load', 'hide');
            }

            $('#title').on('input', function () {
                instance.errorInvalid.title = false;
            });
        },
        methods: {
            setActive: function () {
                this.$emit('setActive', false);
                $('#payment-modal').modal('close');
            },
            setPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            setErrorInvalid() {
                this.error = false;
                this.errorInvalid.title = false;
                this.errorInvalid.type = false;
                this.errorInvalid.enable = false;
                this.errorInvalid.secretkey = false;
                this.errorInvalid.publickey = false;
                this.errorInvalid.clientId = false;
            },
            save() {
                let instance = this;
                if (this.title == undefined || this.title == '') {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.title = true;
                        instance.is_disabled = false;
                    })
                    M.updateTextFields();
                    this.setPreloader('load', 'hide');
                }
                if (this.type == 'stripe' || this.type == 'paypal') {
                    if (this.secretkey == undefined || this.secretkey == '') {
                        this.$nextTick(function () {
                            instance.error = true;
                            instance.errorInvalid.secretkey = true;
                            instance.is_disabled = false;
                        })
                        M.updateTextFields();
                        this.setPreloader('load', 'hide');
                    }
                    if (this.publickey == undefined || this.publickey == '' && this.type == 'stripe') {
                        this.$nextTick(function () {
                            instance.error = true;
                            instance.errorInvalid.publickey = true;
                            instance.is_disabled = false;
                        })
                        M.updateTextFields();
                        this.setPreloader('load', 'hide');
                    }
                    if (this.clientId == undefined || this.clientId == '' && this.type == 'paypal') {
                        this.$nextTick(function () {
                            instance.error = true;
                            instance.errorInvalid.clientId = true;
                            instance.is_disabled = false;
                        })
                        M.updateTextFields();
                        this.setPreloader('load', 'hide');
                    }
                }
                if (this.title) {
                    instance.error = true;
                    if (!this.id) {
                        this.setPreloader('load', '');

                        this.axiosPost('/payment/store', {
                                title: this.title,
                                available_to_client: this.available_to_client,
                                enable: this.enable,
                                type: this.type,
                            },
                            function (response) {
                                M.toast({html:instance.trans('lang.payment_method_saved_successfully')});
                                instance.setActive();
                                instance.is_disabled = false;
                                instance.setPreloader('load', 'hide');
                                instance.$hub.$emit('reloadDataTable');
                            },
                            function (error) {
                                M.toast({html:instance.trans('lang.getting_problems'), classes:'red lighten-1'});
                                instance.setPreloader('load', 'hide');
                                instance.is_disabled = false;
                            });
                    }
                    else {
                        if ((this.type == 'stripe' && this.secretkey && this.publickey) || (this.type == 'paypal' && this.secretkey && this.clientId) || this.type == 'custom') {
                            this.setPreloader('load', '');
                            this.updatePayment();
                        }
                    }
                }
            },
            updatePayment() {
                let instance = this;
                this.axiosPost('/payments/' + this.id, {
                        title: this.title,
                        enable: this.enable,
                        available_to_client: this.available_to_client,
                        secretkey: this.secretkey,
                        publickey: this.publickey,
                        clientId: this.clientId,
                        paypalMode: this.paypalMode,
                    },
                    function (response) {
                        M.toast({html:instance.trans('lang.payment_method_saved_successfully')});
                        instance.setPreloader('load', 'hide');
                        instance.setActive();
                        instance.is_disabled = false;
                        instance.$hub.$emit('reloadDataTable');
                    },
                    function (error) {
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreloader('load', 'hide');
                        instance.is_disabled = false;
                    });
            },
            readData() {
                let instance = this;
                instance.setPreloader('load', '');
                instance.axiosGet('/payments/' + this.id,
                    function (response) {
                        instance.item = response.data;
                        instance.title = instance.item.title;
                        instance.type = instance.item.type;
                        instance.enable = instance.item.enable;
                        instance.available_to_client = instance.item.available_to_client,
                        instance.secretkey = instance.item.option[0];
                        if( instance.type == 'stripe'){
                            instance.publickey = instance.item.option[1];
                        }else if(instance.type == 'paypal'){
                            instance.clientId = instance.item.option[1];

                            if(instance.item.option[2] !== undefined){
                                instance.paypalMode = instance.item.option[2];
                            }
                        }

                        instance.setPreloader('load', 'hide');
                        setTimeout(function () {
                           M.updateTextFields();
                            $('select').formSelect();
                        })
                    }, function (response) {
                        instance.setPreloader('load', 'hide');
                    })
            },
            is_disable() {
                this.is_disabled = true;
            }
        }
    }
</script>