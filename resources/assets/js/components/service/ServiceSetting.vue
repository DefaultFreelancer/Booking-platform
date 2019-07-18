<template>
    <div>
        <div class="row margin-fix modal-layout-header">
            <div class="col s10">
                <h5 class="bluish-text no-margin">{{ trans('lang.setting_service') }}</h5>
            </div>
            <div class="col s2 right-align">
                <a href="#" class="modal-close" @click.prevent="setActive(1,'')"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="modal-layout-content" v-show="hidePreloader=='hide'">
            <div class="row">
                <div class="col s12 m12 l5">
                    <div class="input-field">
                        <div class="label-for-radio">
                            {{ trans('lang.auto_confirm') }}
                        </div>
                    </div>
                    <label for="autoYes">
                    <input type="radio" id="autoYes" class="with-gap" v-model="item.auto_confirm" value="1" name="confirmation"/>
                   <span>{{ trans('lang.yes') }}</span></label>
                    <label for="autoNo">
                    <input type="radio" id="autoNo" class="with-gap" v-model="item.auto_confirm" value="0" name="confirmation"/>
                    <span>{{ trans('lang.no') }}</span></label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12 l5">
                    <div class="input-field">
                        <div class="label-for-radio">
                            {{ trans('lang.activation') }}
                        </div>
                    </div>
                    <label for="yes">
                    <input type="radio" id="yes" class="with-gap" v-model="item.activation" value="1" name="activation"/>
                   <span>{{ trans('lang.yes') }}</span></label>
                    <label for="no">
                    <input type="radio" id="no" class="with-gap" v-model="item.activation" value="0" name="activation"/>
                  <span>{{ trans('lang.no') }}</span></label>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s5">
                    <input v-model="item.alias" name="url" id="url" type="text" class="validate"
                           :class="{'invalid': errorInvalid.url}"   @input="disableError" >
                    <label for="url"  :class="{'active':error}">{{ trans('lang.url_alias') }}</label>
                    <span class="helper-text" :data-error="trans('lang.required_input_field')"></span>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s19 m6 l4">
                    <input v-model="item.allow_booking_max_day_ago" name="max_day_ago" id="max_day_ago" type="text" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57" >
                    <label for="max_day_ago" :data-error="trans('lang.required_input_field')">{{ trans('lang.allow_booking_max_day_ago') }}</label>
                </div>
                <div class="col s3 m3 l3 no-padding">
                    <div class="days-ago-parent">
                        <small class="grey-text days-ago-child">{{ trans('lang.days_ago')}}</small>
                    </div>
                </div>
                <div class="col s12">
                    <button class="btn waves-effect waves-light bluish-back mob-btn" @click.prevent="update(serviceId),is_disable()" :disabled="is_disabled" type="submit">{{ trans('lang.save') }}</button>
                    <button class="btn cancel-btn waves-effect waves-grey mob-btn modal-close" @click.prevent="setActive(1,'')">{{ trans('lang.cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props:['serviceId'],
        data(){
            return{
                allow_cancel:'',
                auto_confirm:'',
                activation:'',
                is_disabled:false,
                allow_booking_max_day_ago:0,
                item:[],
                preloaderType:'',
                hidePreloader:'',
                error: false,
                errorInvalid: {
                    url: false,
                }
            }
        },
        mounted()
        {
            this.readData();
        },
        methods:{
            setActive: function(e, f) {
                this.$emit('setActive', e, f);
            },
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            setError(value) {
                let instance = this;
                instance.$nextTick(function () {
                    instance.error = true;
                    instance.errorInvalid[value] = true;
                });
                instance.setPreloader('load', 'hide');
            },
            disableError(){
                if(this.item.alias !== ''){
                    this.is_disabled = false;
                }
            },
            readData() {
                let instance = this;
                instance.setPreloader('load','');
                this.axiosGet('/serviceid/'+this.serviceId,
                    function(response){
                        instance.item = _.clone(response.data);
                        instance.$nextTick(function(){
                            M.updateTextFields();
                        });
                        instance.setPreloader('load','hide');
                    },
                    function (response) {
                    },
                );
            },
            update(id){
                let instance = this;
                instance.setPreloader('load','');
                if (instance.item.alias === '') {
                    instance.setError('url');
                }else{
                    instance.axiosPost('/serviceSetting/'+id,{
                            allow_cancel:instance.allow_cancel,
                            auto_confirm:this.item.auto_confirm,
                            activation:this.item.activation,
                            allow_booking_max_day_ago:this.item.allow_booking_max_day_ago,
                            alias:this.item.alias,
                        },
                        function(response){
                            instance.setPreloader('load','hide');
                            M.toast({html:response.data.msg});
                            instance.setActive(1,'save');
                            instance.is_disabled = false;
                        },
                        function (error) {
                            instance.setPreloader('load','hide');
                            M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                            instance.is_disabled = false;
                        });
                }


            },
            is_disable()
            {
                this.is_disabled = true;
            }
        },
    }
</script>