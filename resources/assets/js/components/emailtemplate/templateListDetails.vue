<template>
    <div>
        <div class="modal-layout-header">
            <div class="row margin-fix">
                <div class="col s10">
                    <h5 class="bluish-text no-margin">{{ trans('lang.'+name) }}</h5>
                </div>
                <div class="col s2 right-align">
                    <a href="#" class="modal-close" @click.prevent="setActive"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
                </div>
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
                   <div class="input-field col s12">
                       <input v-model="subject" id="subject" type="text" class="validate" :class="{'invalid': errorInvalid.subject}">
                       <label for="subject" :class="{'active': error}">{{ trans('lang.subject') }}</label>
                       <span class="red-text helper-text" :data-error="trans('lang.required_input_field')"></span>
                   </div>
               </div>
                <div class="row">
                    <div class="input-field col s12">
                        <textarea v-model="custom_content" id="custom-content" class="materialize-textarea" :class="{'invalid': errorInvalid.customContent}"></textarea>
                        <small class="red-text"  v-show="errorInvalid.customContent"> {{ trans('lang.required_input_field') }}</small>
                    </div>
                </div>
                <div class="row margin-fix">
                    <div class="col s12" v-if="name=='reset_password'">
                        <div class="chip" v-for="reset in available_variables.reset_password">
                            {{reset}}
                        </div>
                    </div>
                    <div class="col s12" v-if="name=='user_invitation'">
                        <div class="chip" v-for="reset in available_variables.user_invitation">
                            {{reset}}
                        </div>
                    </div>
                    <div class="col s12" v-if="name=='account_verification'">
                        <div class="chip" v-for="reset in available_variables.account_verification">
                            {{reset}}
                        </div>
                    </div>
                    <div class="col s12" v-if="name=='booking_received'">
                        <div class="chip" v-for="reset in available_variables.booking_received">
                            {{reset}}
                        </div>
                    </div>
                    <div class="col s12" v-if="name=='booking_confirmation'">
                        <div class="chip" v-for="reset in available_variables.booking_confirmation">
                            {{reset}}
                        </div>
                    </div>
                    <div class="col s12" v-if="name=='booking_rejected'">
                        <div class="chip" v-for="reset in available_variables.booking_rejected">
                            {{reset}}
                        </div>
                    </div>
                </div>
                <div class="row margin-fix">
                    <div class="input-field col s12">
                        <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" :disabled="is_disabled" @click.prevent="setErrorInvalid(),is_disable(),save()">{{ trans('lang.save') }}</button>
                        <button class="btn cancel-btn waves-effect waves-grey modal-close mob-btn"  @click.prevent="setActive">{{ trans('lang.cancel') }}</button>
                        <button class="btn materialize-red waves-effect waves-light mob-btn right-btn" v-if="isCustom" @click.prevent="restoreToDefault()">{{trans('lang.restore_default')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['id','name'],
        data(){
            return {
                hidePreloader:'',
                preloaderType:'load',
                subject:'',
                custom_content:'',
                isCustom:'',
                available_variables:{
                    reset_password:["{app_name}","{reset_password_link}"],
                    user_invitation:["{app_name}","{invited_by}","{verification_link}"],
                    account_verification:["{app_name}","{first_name}","{last_name}","{verification_link}"],
                    booking_received:["{app_name}","{first_name}","{last_name}","{booking_id}","{service_title}","{booking_quantity}","{booking_status}","{booking_date}","{booking_slot}","{bill}","{payment_status}"],
                    booking_confirmation:["{app_name}","{first_name}","{last_name}","{booking_id}","{service_title}","{booking_quantity}","{booking_status}","{booking_date}","{booking_slot}","{bill}","{payment_status}"],
                    booking_rejected:["{app_name}","{first_name}","{last_name}","{booking_id}","{service_title}","{booking_status}","{booking_quantity}","{booking_date}","{booking_slot}"],
                },
                is_disabled:false,
                error:false,
                errorInvalid:{
                    subject:false,
                    customContent:false,
                }
            }
        },
        mounted(){
            this.getData();
            let instance = this;
            $('#subject').on('input', function() {
                instance.errorInvalid.subject=false;
            });
            $('#custom-content').on('input', function() {
                instance.errorInvalid.customContent=false;
            });
            $("#custom-content").summernote(
                {
                    callbacks: {
                        onChange: function () {
                            let code = $(this).summernote("code");
                            instance.custom_content = code;
                        }
                    }
                }
            );
        },
        methods : {
            setErrorInvalid(){
                this.error=false;
                this.errorInvalid.subject=false;
                this.errorInvalid.customContent=false;
            },
            setActive: function() {
                this.$emit('setActive', false);
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;

            },
            getData(){
                let instance = this;
                instance.setPreloader('load','');
                this.axiosGet('/gettemplatecontent/'+this.id,
                    function(response){
                        instance.subject = response.data.emailSubject;
                        instance.custom_content = response.data.content;
                        instance.isCustom = response.data.isCustom;
                        setTimeout(function () {
                            M.updateTextFields();
                            $("#custom-content").summernote("code", instance.custom_content);
                            instance.setPreloader('load','hide');
                        })
                    },
                    function (response) {
                        instance.setPreloader('load','hide');
                    },
                );
            },
            save()
            {
                let instance = this;
                if(this.subject=='')
                {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.subject = true;
                    })
                    instance.setPreloader('load','hide');
                    instance.is_disabled = false;
                }
                if(this.custom_content=='')
                {
                    this.$nextTick(function () {
                        instance.error = true;
                        instance.errorInvalid.customContent = true;
                    })
                    instance.setPreloader('load','hide');
                    instance.is_disabled = false;
                }
                if(this.subject && this.custom_content)
                {
                    this.saveData();
                }
            },
            restoreToDefault()
            {
                 this.custom_content = '';
                 this.saveData();
            },
            saveData()
            {
                let instance = this;
                instance.setPreloader('load','');
                let tempContent = this.custom_content;
                instance.axiosPost('/setcustomcontent/'+this.id,{
                        subject:this.subject,
                        custom_content:this.custom_content,
                    },
                    function(response){
                        if(tempContent=='')
                        {
                            M.toast({html:instance.trans('lang.email_template_restored_to_default')});
                            instance.getData();
                        }
                        else
                        {
                            M.toast({html:instance.trans('lang.email_templates_successfully_update')});
                            instance.setActive();
                            instance.setPreloader('load','hide');
                            instance.is_disabled = false;
                            $('#email-template-modal').modal('close');
                        }
                    },
                    function (error) {
                        instance.errors = error.data.errors
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreloader('load','hide');
                        instance.is_disabled = false;
                        $('#email-template-modal').modal('close');

                    }
                );
            },
            is_disable()
            {
                this.is_disabled = true;
            },
        },
    }
</script>