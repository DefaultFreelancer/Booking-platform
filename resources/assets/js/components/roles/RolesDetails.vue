<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin" v-if="id">{{ trans('lang.edit_role') }}</h5>
                <h5 class="bluish-text no-margin" v-else>{{ trans('lang.add_role') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close"  @click.prevent="setHide"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <div class="row margin-fix">
                <div class="row margin-fix">
                    <div class="input-field col s12 m8 l8 xl6">
                        <input id="role-title" type="text" v-model="title" class="validate" :class="{'invalid': errorInvalid}">
                        <label for="role-title"  :class="{'active': error}">{{ trans('lang.role_title') }}</label>
                        <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m8 l8 xl6">
                        <div class="row">
                            <h6 >{{trans('lang.settings')}}</h6>
                        </div>
                        <label for="setting-role-1">
                            <input type="checkbox" id="setting-role-1" v-model="check" value="can_edit_application_setting" name="can_edit_application_setting"/>
                            <span for="setting-role-1">{{ trans('lang.can_manage_application_setting') }}</span> <br>
                        </label>
                        <label for="setting-role-2">
                            <input type="checkbox" id="setting-role-2" v-model="check" value="can_edit_email_setting" name="can_edit_email_setting"/>
                            <span for="setting-role-2">{{ trans('lang.can_manage_email_setting') }}</span> <br>
                        </label>
                        <label for="setting-role-3">
                            <input type="checkbox" id="setting-role-3" v-model="check" value="can_edit_off_day_setting" name="can_edit_off_day_setting"/>
                            <span for="setting-role-3">{{ trans('lang.can_manage_offday_setting') }}</span> <br>
                        </label>
                        <label for="setting-role-4">
                            <input type="checkbox" id="setting-role-4" v-model="check" value="can_edit_holi_day_setting" name="can_edit_holi_day_setting"/>
                            <span for="setting-role-4">{{ trans('lang.can_manage_holiday_setting') }}</span> <br>
                        </label>
                        <label for="email-role-1">
                            <input type="checkbox" id="email-role-1" v-model="check" value="can_edit_email_template" name="can_edit_email_template"/>
                            <span for="email-role-1">{{ trans('lang.can_manage_email_template') }}</span> <br>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m8 l8 xl6">
                        <div class="row">
                            <h6>{{trans('lang.service')}}</h6>
                        </div>
                        <label for="service-role-1">
                            <input type="checkbox" id="service-role-1" v-model="check" value="can_add_service" name="can_add_service"/>
                            <span>{{ trans('lang.can_manage_service') }}</span><br>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12 m8 l8 xl6">
                        <div class="row">
                            <h6>{{trans('lang.booking')}}</h6>
                        </div>
                        <label for="booking-role-1">
                            <input type="checkbox" id="booking-role-1" v-model="check" value="can_add_booking" name="can_add_booking"/>
                            <span for="booking-role-1">{{ trans('lang.can_add_booking_for_users') }}</span> <br>
                        </label>
                        <label for="booking-role-2">
                            <input type="checkbox" id="booking-role-2" v-model="check" value="can_manage_booking" name="can_manage_booking"/>
                            <span for="booking-role-2">{{ trans('lang.can_manage_booking') }}</span> <br>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="row">
                            <h6>{{trans('lang.clients')}}</h6>
                        </div>
                        <label for="client-setting-role-1">
                            <input type="checkbox" id="client-setting-role-1" v-model="check" value="can_manage_client_setting" name="can_manage_client_setting"/>
                            <span for="client-setting-role-1">{{ trans('lang.can_manage_client_settings') }}</span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="row">
                            <h6>{{trans('lang.reports')}}</h6>
                        </div>
                        <label for="report-role-1">
                            <input type="checkbox" id="report-role-1" v-model="check" value="can_see_reports" name="can_see_reports"/>
                            <span for="report-role-1">{{ trans('lang.can_see_reports') }}</span> <br>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <div class="col s12">
                        <div class="row">
                            <h6>{{trans('lang.payment_methods')}}</h6>
                        </div>
                        <label for="manage-payment-methods">
                            <input type="checkbox" id="manage-payment-methods" v-model="check" value="can_manage_payment_methods" name="can_manage_payment_methods"/>
                            <span for="manage-payment-methods">{{ trans('lang.can_manage_payment_methods') }}</span> <br>
                        </label>
                    </div>
                </div>
                <div class="row margin-fix">
                    <div class="col s12 m8 l8 xl6">
                        <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="is_disable(),errorInvalid=false,error=false,save()" :disabled="is_disabled">{{ trans('lang.save') }}</button>
                        <button class="btn cancel-btn waves-effect waves-grey mob-btn" @click.prevent="setHide()">{{ trans('lang.cancel') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default
    {
        extends: axiosGetPost,
        props:['id','name'],
        data(){
            return{
                title:'',
                check:[],
                add_role:'',
                delete_user:'',
                array:'',
                isSubmitted:false,
                error:false,
                errorInvalid:false,
                invalidMessage: '',
                is_disabled:false,
                preloaderType:'load',
                hidePreloader:'',
            }
        },
        mounted(){
            this.setData();
            var instance = this;
            $('#role-title').on('input', function() {
                instance.errorInvalid=false;
            });
        },
        methods: {
            roleAddUpdate() {
                let instance = this;
                this.$hub.$on('roleAddUpdate', function (id, name) {
                    instance.id = id;
                    instance.name = name;
                    instance.setData();
                });
            },
            setHide: function() {
                this.$emit('setHide', 'hide', true);
                $('#roles-modal').modal('close');
            },
            setPreloader: function(type,action) {
                //this.$emit('setPreloader', e,f);
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            save()
            {
                var instance = this;
                if(this.title=='')
                {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid = true;
                    })
                    instance.is_disabled = false;
                    this.setPreloader('load','hide');
                }
                else
                {
                    this.setPreloader('load','')
                    if (!this.id) {
                        instance.axiosPost('/addrole',{
                                title: this.title,
                                permissions: this.check,
                            },
                            function(response){
                                M.toast({html:instance.trans('lang.roles_saved_successfully')});
                                instance.setHide();
                                instance.setPreloader('load','hide');
                                instance.is_disabled = false;
                                instance.$hub.$emit('reloadDataTable');
                            },
                            function (error) {
                                instance.errors = error.data.errors
                                M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                                instance.setPreloader('load','hide')
                                instance.is_disabled = false;
                            }
                        );
                    }
                    else {
                        instance.axiosPost('/addrole/' + this.id,{
                                title: this.title,
                                permissions: this.check,
                            },
                            function(response){
                                M.toast({html:instance.trans('lang.roles_saved_successfully')});
                                instance.setHide();
                                instance.setPreloader('load','hide');
                                instance.is_disabled = false;
                                instance.$hub.$emit('reloadDataTable');
                            },
                            function (error) {
                                instance.errors = error.data.errors
                                M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                                instance.setPreloader('load','hide');
                                instance.is_disabled = false;
                            }
                        );

                    }
                }
            },
            setData(){
                var instance = this;
                instance.setPreloader('load','')
                this.axiosGet('/rolepermissions/'+this.id,
                    function(response){
                        instance.check = response.data;
                        instance.title = instance.name;
                        setTimeout(function () {
                            M.updateTextFields();
                            instance.setPreloader('load','hide')
                        })
                    },
                    function (response) {
                        instance.setPreloader('load','hide')
                    },
                );
            },
            is_disable()
            {
                this.is_disabled = true;
            }
        }
    }
</script>