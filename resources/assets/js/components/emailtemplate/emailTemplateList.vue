<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.email_templates') }}</h5>
                </div>
            </div>
        </div>
        <div class="main-layout-card-content">
            <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                <div class="modal-loader-child">
                    <preloaders :loderType="preloaderType"></preloaders>
                </div>
            </div>
            <div class="main-layout-card-content" v-show="hidePreloader=='hide'">
                <div class="row margin-fix">
                    <datatable-component :options="tableOptions" @setPreloader="getPreloader"></datatable-component>
                </div>
            </div>
        </div>
        <div id="email-template-modal" class="modal modal-layout">
            <email-template-list-details v-if="isActive" :id="templateId" :name="template_name" @setActive="getActive"></email-template-list-details>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data() {
            let instance = this;
            return{
                isActive: false,
                items:[],
                templateId:'',
                template_name:'',
                hidePreloader:'',
                preloaderType:'load',
                tableOptions: {
                    tableName: 'email_templates',
                    columns: [
                        {title: 'lang.title', key: 'template_type', type: 'language', sortable: true},
                        {title: 'lang.action', type: 'component', componentName: 'email-template-action-component'}
                    ],
                    source:'/templatelist'
                }
            }
        },
        mounted(){
            let instance = this;
            $('#email-template-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function()
                    {
                        instance.getPreloader('load','hide');
                        instance.isActive = false;
                        instance.renderTooltip('');

                    },
                    onCloseEnd:function () {
                        instance.isActive = false;
                    }

                }
            );
            this.$hub.$on('viewTemplateEdit', function (id,title) {
                instance.viewTemplate(id,title);
            })
            this.$hub.$on('renderTooltip', function(value){
                instance.renderTooltip(value);
            });

            // $('#email-template-modal').on('hidden.bs.modal', function () {
            //     window.alert('hidden event fired!');
            // });
        },
        methods : {
            getActive: function( activity ){
                this.isActive = activity;
            },
            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            readData() {
                let instance = this;
                instance.getPreloader('load','');
                this.axiosGet('/templatelist',
                    function(response){
                        instance.items = response.data;
                        setTimeout(function () {
                            instance.renderTooltip('');
                        });
                        instance.getPreloader('load','hide');
                    },
                    function (response) {
                        instance.getPreloader('load','hide');
                    },
                );
            },
            viewTemplate(id,title)
            {
                this.templateId = id;
                this.template_name = title;
                this.isActive = true;
            },
            renderTooltip(t)
            {
                $('.tooltipped').tooltip(t);
            }
        },
    }
</script>