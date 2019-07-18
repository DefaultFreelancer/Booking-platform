<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.email_settings') }}</h5>
                </div>
            </div>
        </div>
        <div class="main-layout-card-content">
            <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                <div class="modal-loader-child">
                    <preloaders :loderType="preloaderType"></preloaders>
                </div>
            </div>
            <div class="row margin-fix" v-show="hidePreloader=='hide'">
                <form>
                    <div class="input-field col s12 m12 l5">
                        <input id="appName" v-model="item.email_from_name" type="text" class="validate" :class="{'invalid': errorInvalid.appName}" @input="errorInvalid.appName = false">
                        <label for="appName"  :class="{'active': error}">{{ trans('lang.application_name') }}</label>
                        <span class="helper-text" :data-error="errorMessage.appName"></span>
                    </div>
                    <div class="input-field col s12 m12 l5 offset-l1">
                        <input id="emailAddress" v-model="item.email_from_address" type="email" class="validate" :class="{'invalid': errorInvalid.email}"  @input="errorInvalid.email = false,errorMessage.email = trans('lang.invalid_email')">
                        <label for="emailAddress" :class="{'active': error}">{{ trans('lang.login_email') }}</label>
                        <span class="helper-text" :data-error="errorMessage.email"></span>
                    </div>
                    <div class="input-field col s12 m12 l5">
                        <select id="email_driver" class="" v-model="item.email_driver">
                            <option value="" disabled>{{ trans('lang.choose_one') }}</option>
                            <option value="smtp"> {{ trans('lang.smtp') }} </option>
                            <option value="sendmail"> {{ trans('lang.sendmail') }} </option>
                            <option value="mandrill"> {{ trans('lang.mandrill') }} </option>
                            <option value="sparkpost"> {{ trans('lang.sparkpost') }} </option>
                            <option value="mailgun"> {{ trans('lang.mailgun') }} </option>
                            <!--<option value="ses"> {{ trans('lang.ses') }} </option>-->
                        </select>
                        <label for="email_driver" >{{ trans('lang.email_driver') }}</label>
                        <span v-if="errorInvalid.driver" class="red-text helper-text">{{ trans('lang.please_choose_one') }}</span>
                    </div>
                    <div v-if="item.email_driver == 'smtp'">
                        <div class="input-field col s12 m12 l5 offset-l1">
                            <input id="emailHost" v-model="item.email_smtp_host" type="text" class="validate" :class="{'invalid': errorInvalid.host}"  @input="errorInvalid.host = false">
                            <label for="emailHost"  :class="{'active': error2}">{{ trans('lang.host') }}</label>
                            <span class="red-text helper-text" :data-error="errorMessage.host"></span>
                        </div>
                        <div class="input-field col s12 m12 l5">
                            <input id="emailPort" v-model="item.email_port" type="number" class="validate" :class="{'invalid': errorInvalid.port}"  @input="errorInvalid.port = false">
                            <label for="emailPort"  :class="{'active': error2}">{{ trans('lang.port') }}</label>
                            <span class="red-text helper-text" :data-error="errorMessage.port"></span>
                        </div>
                        <div class="input-field col s12 m12 l5 offset-l1">
                            <input id="password" v-model="item.email_smtp_password" :type="changeType ? 'password' : 'text'" class="validate" :class="{'invalid': errorInvalid.password}"  @input="errorInvalid.password = false,changeType = true">
                            <label for="password"  :class="{'active': error2}">{{ trans('lang.password_email_settings') + item.email_from_address }}</label>
                            <span class="helper-text" :data-error="errorMessage.password"></span>
                        </div>
                        <div class="input-field col s12 m12 l5">
                            <select id="encryption_type" class="" v-model="item.email_encryption_type">
                                <option value="">{{ trans('lang.choose_one') }}</option>
                                <option value="tls">{{ trans('lang.tls') }}</option>
                                <option value="ssh">{{ trans('lang.ssh') }}</option>
                            </select>
                            <label for="encryption_type">{{ trans('lang.encryption_type') }}</label>
                            <span v-if="errorInvalid.encryptionType" class="red-text helper-text">{{ trans('lang.please_choose_one') }}</span>
                        </div>
                    </div>
                    <div v-if="item.email_driver == 'mailgun'">
                        <div class="input-field col s12 m12 l5 offset-l1">
                            <input id="mailgunDomain" v-model="item.mailgun_domain" type="text" class="validate" :class="{'invalid': errorInvalid.mailgunDomain}"  @input="errorInvalid.mailgunDomain = false">
                            <label for="mailgunDomain"  :class="{'active': error2}">{{ trans('lang.mailgun_domain') }}</label>
                            <span class="helper-text" :data-error="errorMessage.mailgunDomain"></span>
                        </div>
                        <div class="input-field col s12 m12 l5">
                            <input id="mailgunApi" v-model="item.mailgun_api" type="text" class="validate" :class="{'invalid': errorInvalid.mailgunApi}"  @input="errorInvalid.mailgunApi = false">
                            <label for="mailgunApi"  :class="{'active': error2}">{{ trans('lang.mailgun_api') }}</label>
                            <span class="helper-text" :data-error="errorMessage.mailgunApi"></span>
                        </div>
                    </div>
                    <div class="input-field col s12 m12 l5 offset-l1">
                        <input id="test_mail" v-model="test_mail" type="text" :placeholder="trans('lang.type_mail_address_to_check_email_config')" :class="{'invalid': testMailErrorInactive}" @change="testMailCheck" @input="testMailErrorInactive = false">
                        <label for="test_mail"  :class="{'active':testMailError}">{{ trans('lang.test_mail') }}</label>
                        <span class="helper-text" :data-error="testMailErrorMessage"></span>
                    </div>
                    <div v-if="item.email_driver == 'mandrill'">
                        <div class="input-field col s12 m12 l5">
                            <input id="mandrillApi" v-model="item.mandrill_api" type="text" class="validate" :class="{'invalid': errorInvalid.mandrillApi}"  @input="errorInvalid.mandrillApi = false">
                            <label for="mandrillApi"  :class="{'active': error2}">{{ trans('lang.mandrill_api') }}</label>
                            <span class="helper-text" :data-error="errorMessage.mandrillApi"></span>
                        </div>
                        <!--<div class="input-field col s12 m12 l5">-->
                            <!--<input id="mandrillApi" v-model="item.mandrill_api" type="text" class="validate" :class="{'invalid': errorInvalid.mandrillApi}"  @input="errorInvalid.mandrillApi = false">-->
                            <!--<label for="mandrillApi" :data-error="errorMessage.mandrillApi" :class="{'active': error2}">{{ trans('lang.mandrill_api') }}</label>-->
                        <!--</div>-->
                    </div>
                    <div v-if="item.email_driver == 'sparkpost'">
                        <div class="input-field col s12 m12 l5">
                            <input id="sparkpostApi" v-model="item.sparkpost_api" type="text" class="validate" :class="{'invalid': errorInvalid.sparkpostApi}"  @input="errorInvalid.sparkpostApi = false">
                            <label for="sparkpostApi" :data-error="errorMessage.sparkpostApi" :class="{'active': error2}">{{ trans('lang.sparkpost_api') }}</label>
                            <span class="helper-text" :data-error="errorMessage.sparkpostApi"></span>
                        </div>
                    </div>
                    <!--<div v-if="item.email_driver == 'ses'">-->
                        <!--<div class="input-field col s12 m12 l5">-->
                            <!--<input id="ses_key" v-model="item.ses_key" type="text" class="validate" :class="{'invalid': errorInvalid.sesKey}"  @input="errorInvalid.sesKey = false">-->
                            <!--<label for="ses_key" :data-error="errorMessage.sesKey" :class="{'active': error2}">{{ trans('lang.ses_key') }}</label>-->
                        <!--</div>-->
                        <!--<div class="input-field col s12 m12 l5 offset-l1">-->
                            <!--<input id="ses_secret" v-model="item.ses_secret" type="text" class="validate" :class="{'invalid': errorInvalid.sesSecret}"  @input="errorInvalid.sesSecret = false">-->
                            <!--<label for="ses_secret" :data-error="errorMessage.sesSecret" :class="{'active': error2}">{{ trans('lang.ses_secret') }}</label>-->
                        <!--</div>-->
                        <!--<div class="input-field col s12 m12 l5">-->
                            <!--<input id="ses_region" v-model="item.ses_region" type="text" class="validate" :class="{'invalid': errorInvalid.sesRegion}" @input="errorInvalid.sesRegion = false">-->
                            <!--<label for="ses_region" :data-error="errorMessage.sesRegion" :class="{'active': error2}">{{ trans('lang.ses_region') }}</label>-->
                        <!--</div>-->
                    <!--</div>-->
                    <div class="input-field col s12" :class="{'error-m': errorInvalid.encryptionType || errorInvalid.driver}">
                        <button class="btn waves-effect waves-light bluish-back mob-btn" type="submit" @click.prevent="is_disable(),setErrorInvalid(),updateEmailSetting()" :disabled="is_disabled">{{ trans('lang.save')}}</button>
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
                email_from_name:'',
                email_from_address:'',
                email_driver:'',
                email_smtp_host:'',
                email_port:'',
                email_smtp_password:'',
                email_encryption_type:'',
                mandrill_api:'',
                sparkpost_api:'',
                mailgun_domain:'',
                mailgun_api:'',
                // ses_key:'',
                // ses_secret:'',
                // ses_region:'',
                item:[],
                changeType: false,
                isSubmitted:false,
                hidePreloader:'',
                preloaderType:'load',
                error:false,
                error2:false,
                is_disabled:false,
                test_is_disabled:false,
                test_mail:'',
                testMailError:false,
                testMailErrorInactive:false,
                testMailErrorMessage: this.trans('lang.invalid_email'),
                errorInvalid:{
                    appName:false,
                    email:false,
                    driver:false,
                    host:false,
                    port:false,
                    password:false,
                    mailgunDomain:false,
                    mailgunApi:false,
                    encryptionType:false,
                    mandrillApi:false,
                    sparkpostApi:false,
                    // sesKey:false,
                    // sesSecret:false,
                    // sesRegion:false,
                },
                errorMessage:{
                    appName:this.trans('lang.required_input_field'),
                    email:this.trans('lang.required_input_field'),
                    host:this.trans('lang.required_input_field'),
                    port:this.trans('lang.required_input_field'),
                    password:this.trans('lang.required_input_field'),
                    mandrillApi:this.trans('lang.required_input_field'),
                    sparkpostApi:this.trans('lang.required_input_field'),
                    mailgunDomain:this.trans('lang.required_input_field'),
                    mailgunApi:this.trans('lang.required_input_field'),
                    // sesKey:this.trans('lang.required_input_field'),
                    // sesSecret:this.trans('lang.required_input_field'),
                    // sesRegion:this.trans('lang.required_input_field'),
                }
            }
        },
        created(){
            this.readData();
        },
        mounted(){
            $('select').formSelect();
            M.updateTextFields();
        },
        methods:{

            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            setErrorInvalid()
            {
                this.error=false;
                this.error2=false;
                this.errorInvalid.appName=false
                this.errorInvalid.email=false;
                this.errorInvalid.host=false
                this.errorInvalid.port=false
                this.errorInvalid.password=false
                this.errorInvalid.mailgunApi=false
                this.errorInvalid.mailgunDomain=false
                this.errorInvalid.mandrillApi=false
                this.errorInvalid.sparkpostApi=false
                // this.errorInvalid.sesKey=false
                // this.errorInvalid.sesSecret=false
                // this.errorInvalid.sesRegion=false
            },
            isSelected(){
                if(this.item.email_encryption_type.length==0)
                {
                    return true;
                }
                else return '';
            },
            readData()
            {
                let instance = this;
                instance.setPreloader('load','');
                this.axiosGet('/emailsettingdata',
                    function(response){
                        instance.item = response.data;
                        setTimeout(function () {
                            if(instance.item.email_smtp_password)
                            {
                                instance.changeType = true;
                            }
                            instance.setPreloader('load','hide');
                            M.updateTextFields();
                            $('select').formSelect();
                            $('#email_driver').on('change', function() {
                                let value = $(this).val();
                                instance.item.email_driver = value;
                                instance.errorInvalid.driver = false;
                                if(instance.item.email_driver == 'smtp')
                                {
                                    instance.$nextTick(function(){
                                        M.updateTextFields();
                                        $('#encryption_type').formSelect();
                                        $('#encryption_type').on('change', function() {
                                            let value = $(this).val();
                                            instance.item.email_encryption_type = value;
                                            instance.errorInvalid.encryptionType = false;
                                            instance.is_disabled = false;
                                        });
                                    });
                                    if(instance.errorInvalid.host==true || instance.errorInvalid.port==true || instance.errorInvalid.password==true || instance.errorInvalid.encryptionType == true)
                                    {
                                        instance.error2 = true;
                                    }
                                    else
                                    {
                                        instance.error2 = false;
                                    }
                                }
                                else
                                {
                                    instance.errorInvalid.host=false;
                                    instance.errorInvalid.port=false;
                                    instance.errorInvalid.password=false;
                                    instance.errorInvalid.encryptionType = false;
                                    instance.$nextTick(function(){
                                        M.updateTextFields();
                                    })
                                }
                            });
                        });
                    },
                    function (error) {
                        instance.setPreloader('load','hide');
                    },
                );
            },
            setError(value){
                let instance = this;
                this.$nextTick(function () {
                    instance.error = true;
                    instance.error2 = true;
                    instance.errorInvalid[value] = true;
                })
            },
            updateEmailSetting()
            {
                let instance = this;
                if(this.item.email_from_name=='') {instance.setError('appName');instance.is_disabled = false;}
                if(this.item.email_driver=='') {instance.setError('driver');instance.is_disabled = false;}
                if(this.item.email_driver == 'smtp')
                {
                    if(this.item.email_smtp_host=='') {instance.setError('host');instance.is_disabled = false;}
                    if(this.item.email_port=='') {instance.setError('port');instance.is_disabled = false;}
                    if(this.item.email_smtp_password=='') {instance.setError('password');instance.is_disabled = false;}
                    if(this.item.email_encryption_type=='') {instance.setError('encryptionType');instance.is_disabled = false;}
                }
                if(this.item.email_driver == 'mandrill'){
                    if(this.item.mandrill_api=='') {instance.setError('mandrillApi');instance.is_disabled = false;}
                }
                if(this.item.email_driver == 'sparkpost'){
                    if(this.item.sparkpost_api=='') {instance.setError('sparkpostApi');instance.is_disabled = false;}
                }
                if(this.item.email_driver == 'mailgun'){
                    if(this.item.mailgun_domain=='') {instance.setError('mailgunDomain');instance.is_disabled = false;}
                    if(this.item.mailgun_api=='') {instance.setError('mailgunApi');instance.is_disabled = false;}
                }
        //NB: dont remove below comment
                // if(this.item.email_driver == 'ses'){
                //     if(this.item.ses_key=='') {instance.setError('sesKey');instance.is_disabled = false;}
                //     if(this.item.ses_secret=='') {instance.setError('sesSecret');instance.is_disabled = false;}
                //     if(this.item.ses_region=='') {instance.setError('sesRegion');instance.is_disabled = false;}
       // }
                let emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                if(!this.item.email_from_address.match(emailFormat))
                {
                    if(this.item.email_from_address=='')
                    {
                        instance.errorMessage.email = instance.trans('lang.required_input_field');
                    }
                    else{
                        instance.errorMessage.email = instance.trans('lang.invalid_email');
                    }
                    instance.setError('email');
                }
                if(this.item.email_driver == 'smtp' && this.item.email_from_name && this.item.email_from_address.match(emailFormat)  && this.item.email_smtp_host && this.item.email_port && this.item.email_smtp_password && this.item.email_encryption_type )
                {
                    instance.saveData(emailFormat);
                }
                else if(this.item.email_driver == 'sendmail' && this.item.email_from_name && this.item.email_from_address.match(emailFormat))
                {
                    instance.saveData(emailFormat);
                }
                else if (this.item.email_driver == 'mandrill' && this.item.email_from_name && this.item.email_from_address.match(emailFormat) && this.item.mandrill_api){
                    instance.saveData(emailFormat);
                }
                else if(this.item.email_driver == 'sparkpost' && this.item.email_from_name && this.item.email_from_address.match(emailFormat) && this.item.sparkpost_api){
                    instance.saveData(emailFormat);
                }
                else if(this.item.email_driver == 'mailgun' && this.item.email_from_name && this.item.email_from_address.match(emailFormat) && this.item.mailgun_domain && this.item.mailgun_api){
                    instance.saveData(emailFormat);
                }
                // else if(this.item.email_driver == 'ses' && this.item.email_from_name && this.item.email_from_address.match(emailFormat) && this.item.ses_key && this.item.ses_secret && this.item.ses_region){
                //     instance.saveData(emailFormat);
                // }
                else
                {
                    instance.is_disabled = false;
                }
            },

            saveData(emailFormat)
            {
                let instance = this;
                instance.error = true;
                instance.error2 = true;
                instance.setPreloader('load','');
                instance.axiosPost('/emailsetting',{
                        email_from_name: this.item.email_from_name,
                        email_from_address: this.item.email_from_address,
                        email_driver: this.item.email_driver,
                        email_smtp_host: this.item.email_smtp_host,
                        email_port: this.item.email_port,
                        email_smtp_password: this.item.email_smtp_password,
                        email_encryption_type: this.item.email_encryption_type,
                        mandrill_api: this.item.mandrill_api,
                        sparkpost_api: this.item.sparkpost_api,
                        mailgun_domain:this.item.mailgun_domain,
                        mailgun_api:this.item.mailgun_api,
                        // ses_key:this.item.ses_key,
                        // ses_secret:this.item.ses_secret,
                        // ses_region:this.item.ses_region,
                        test_mail:this.test_mail,
                    },
                    function(response){
                        if(instance.test_mail.match(emailFormat) && response.data.message)
                        {
                            M.toast({html:instance.trans('lang.email_settings_successfully_saved_&_ test_mail_is_sent')});
                        }
                        else {
                            M.toast({html:instance.trans('lang.email_settings_successfully_saved')});
                        }
                        instance.setPreloader('load','hide');
                        instance.is_disabled = false;
                    },
                    function (error) {
                        if(error.data.message)
                        {
                            M.toast({html:instance.trans('lang.email_not_sent'),classes:'red lighten-1'});
                        }
                        else
                        {
                            M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        }
                        instance.setPreloader('load','hide');
                        instance.is_disabled = false;
                    }
                );
            },
            testMailCheck(event)
            {
                let emailFormat = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                if (this.test_mail && !event.target.value.match(emailFormat))
                {
                    this.testMailErrorInactive = true;
                }
                else this.testMailErrorInactive = false;
            },
            is_disable()
            {
                this.is_disabled = true;
            },
            test_is_disable()
            {
                this.test_is_disabled = true;
            }
        }
    }
</script>