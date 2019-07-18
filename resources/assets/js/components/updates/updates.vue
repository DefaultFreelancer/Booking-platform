<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.updates') }}</h5>
                </div>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
                <h5 class="center-align" v-if="!installingVersion">{{ trans('lang.checking_for_updates')}}</h5>
                <h5 class="center-align" v-else>{{ trans('lang.installing_version') +' '+installingVersion}}</h5>
            </div>
        </div>
        <div class="main-layout-card-content" v-else>
            <div class="row" v-if="updates.length>0">
                <div class="backup-reminder center-align red lighten-2 white-text">
                    {{trans('lang.backup_reminder')}}
                </div>
            </div>
            <div class="row">
               {{ trans('lang.current_version') +': '+ version }}
            </div>
            <div class="row margin-fix" v-if="updates.length>0">
                <h6>{{trans('lang.available_updates')}}:</h6>
                <li v-for="update in updates">
                    <a href="#" @click.prevent="unzipData(update.version)">{{ trans('lang.click_to_install_version')+' '+ update.version }}</a>
                </li>
            </div>
            <div v-else>
                {{ trans('lang.no_updates_found') }}
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
                version:"",
                updates:[],
                noUpdates:'',
                preloaderType:'',
                hidePreloader:'',
                installingVersion:'',

            }
        },
        mounted(){
            this.currentVersion();
        },
        methods: {
            setPreloader(type,action)
            {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            currentVersion() {
                let instance = this;
                instance.setPreloader('load','');
                this.axiosGet('/gain-update/',
                    function(response){
                        let app_version = response.data;
                        instance.version = app_version.app_version;
                        instance.downloadUpdates();
                    },
                    function (response) {
                        instance.setPreloader('load','hide');
                    },
                );
            },
            versionUpdates() {
                let instance = this;
                this.axiosGet('/update-version-list/',
                    function(response){
                        instance.updates = response.data;
                        instance.setPreloader('load','hide');
                    },
                    function (response) {
                        instance.setPreloader('load','hide');
                    },
                );
            },
            downloadUpdates() {
                let instance = this;
                this.axiosGet('/curl_get_contents/',
                    function(response){
                        instance.versionUpdates();
                    },
                    function (error) {
                        M.toast({html:error.response.data.message,classes:'red lighten-1'});
                        instance.setPreloader('load','hide');
                    },
                );
            },

            unzipData(update){
                let instance = this;
                this.installingVersion = update;
                this.setPreloader('load','');
                instance.axiosPost('/install-new-version/'+update,{
                    },
                    function(response){
                        M.toast({html:response.data.message})
                        instance.setPreloader('load','hide');
                        window.location.reload();
                    },
                    function (error) {
                        M.toast({html:error.data.message,classes:'red lighten-1'});
                        instance.setPreloader('load','hide');

                    }
                );
            },
        }
    }
</script>