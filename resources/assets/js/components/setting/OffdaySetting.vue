<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.off_day_settings') }}</h5>
                </div>
            </div>
        </div>
        <div class="main-layout-card-content">
            <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                <div class="modal-loader-child">
                    <preloaders :loderType="preloaderType"></preloaders>
                </div>
            </div>
            <div v-show="hidePreloader=='hide'">
                <div class="row">
                    <h6>{{ trans('lang.choose_off_day')}}</h6>
                </div>
                <div class="row">
                    <label for="1">
                    <input type="checkbox" id="1" value=1 v-model="offday_setting">
                    <span>{{ trans('lang.sunday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="2">
                    <input type="checkbox" id="2" value=2 v-model="offday_setting">
                    <span>{{ trans('lang.monday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="3">
                    <input type="checkbox" id="3" value=3 v-model="offday_setting">
                    <span>{{ trans('lang.tuesday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="4">
                    <input type="checkbox" id="4" value=4 v-model="offday_setting">
                    <span>{{ trans('lang.wednesday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="5">
                    <input type="checkbox" id="5" value=5 v-model="offday_setting">
                    <span>{{ trans('lang.thursday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="6">
                    <input type="checkbox" id="6" value=6 v-model="offday_setting">
                   <span>{{ trans('lang.friday') }}</span></label> <span class="offday-checkbox-space"></span>
                    <br>
                    <label for="7">
                    <input type="checkbox" id="7" value=7 v-model="offday_setting">
                   <span>{{ trans('lang.saturday') }}</span></label> <span class="offday-checkbox-space"></span>
                </div>
                <div class="row margin-fix">
                    <button class="btn waves-effect waves-light bluish-back mob-btn" @click.prevent="is_disable(),createOffdays()" type="submit" :disabled="is_disabled">{{ trans('lang.save') }}</button>
                </div>
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
                offday_setting: [],
                hidePreloader:'',
                is_disabled:false,
                preloaderType:'load',
            }
        },
        created(){
            this.readData();
        },
        methods:{
            setPreloader: function(type,action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            readData() {
                let instance = this;
                instance.setPreloader('load','');
                instance.axiosGet('/offdaysdata',
                    function (response) {
                        instance.offday_setting = response.data.offday_setting;
                        instance.setPreloader('load','hide');
                    },function (response)
                    {
                        instance.setPreloader('load','hide');
                    });
            },
            createOffdays() {
                let instance = this;
                instance.setPreloader('load','');

                instance.axiosPost('/offdaysetting',{
                        offday_setting: this.offday_setting,
                    },
                    function(response){
                        instance.setPreloader('load','hide');
                        M.toast({html:instance.trans('lang.offday_successfully_saved')});
                        instance.is_disabled = false;
                    },
                    function (error) {
                        instance.errors = error.data.errors
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.is_disabled = false;
                        instance.setPreloader('load','hide');
                    });
            },
            is_disable()
            {
                this.is_disabled = true;
            }
        }
    }
</script>