<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header-with-button">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.roles') }}</h5>
                </div>
                <div class="main-layout-card-header-contents right-align">
                    <button class="btn waves-effect waves-light bluish-back modal-trigger" data-target="roles-modal" @click.prevent=" seen = false , viewRole('',''), roleHide =''">{{ trans('lang.add') }}</button>
                </div>
            </div>
        </div>
        <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
            <div class="modal-loader-child">
                <preloaders :loderType="preloaderType"></preloaders>
            </div>
        </div>
        <div class="main-layout-card-content" v-show="hidePreloader=='hide'">
            <div class="row margin-fix">
                <datatable-component :options="tableOptions" @setPreloader="getPreloader" ></datatable-component>
            </div>
        </div>
        <div id="roles-modal" class="modal modal-layout">
            <roles-details v-if="seen==false" v-on:setHide="getHide" :id="selectedItemId" :name="title"></roles-details>
        </div>
        <div id="confirm-delete" class="modal modal-layout confirm-delete-modal">
            <confirmation-modal :message = "'role_deleted_permanently'" :firstButtonName="'yes'" :secondButtonName = "'no'" @confirmationModalButtonAction = "confirmationModalButtonAction"></confirmation-modal>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data() {
            return {
                seen: true,
                roleHide: 'hide',
                items:[],
                selectedItemId:'',
                title:'',
                permissions:'',
                deleteID:'',
                deleteIndex:'',
                windowWidth: 0,
                deletePreloader:'hide',
                hidePreloader:'',
                preloaderType:'load',
                tableOptions: {
                    tableName: 'roles',
                    columns: [
                        {title: 'lang.title', key: 'title', type: 'text', sortable: true, },
                        {title: 'lang.action', type: 'component', componentName: 'roles-action-component'}
                    ],
                    source:'/roletitle'
                }
            }
        },
        mounted(){
            let instance = this;
            $('#confirm-delete').modal({
                    dismissible: true, // Modal can be dismissed by clicking outside of the modal
                    opacity: .5, // Opacity of modal background
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    startingTop: '0', // Starting top style attribute
                    endingTop: '0', // Ending top style attribute
                }
            );
            $('#roles-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function()
                    {
                        instance.renderTooltip('');
                        instance.seen = true;
                    },
                    onCloseEnd:function () {
                        instance.seen = true;
                    }
                }
            );
            this.$nextTick(function () {
                window.addEventListener('resize', this.getWindowWidth);
                this.getWindowWidth()
            });
            this.$hub.$on('changeRoleDeleteId',function (id,index) {
                instance.deleteID = id;
                instance.deleteIndex = index;
            })
            this.$hub.$on('viewRoleEditData', function (id,title) {
                instance.seen = false;
                instance.roleHide = '';
                instance.viewRole(id,title);
            })
            this.$hub.$on('renderTooltip', function(value){
                instance.renderTooltip(value);
            });
        },
        methods: {
            confirmationModalButtonAction()
            {
                this.deleteRole(this.deleteID);
            },
            getHide: function(newHide,newSeen){
                this.roleHide = newHide;
                this.seen = newSeen;
            },
            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            getWindowWidth(event) {
                this.windowWidth = document.documentElement.clientWidth;
            },
            viewRole($id,$name){
                this.selectedItemId =$id;
                this.title = $name;
            },
            deleteRole(id)
            {
                var instance = this
                instance.getPreloader('delete','');
                instance.axiosPost('/deleterole/'+id,{
                    },
                    function(response){
                        instance.$hub.$emit('updateDataAfterDelete',instance.deleteIndex);
                        M.toast({html:instance.trans('lang.roles_deleted_successfully')})
                        instance.getPreloader('delete','hide');
                        instance.renderTooltip('');
                    },
                    function (error) {
                        instance.errors = error.data.errors
                        M.toast({html:instance.trans('lang.role_is_used'),classes:'red lighten-1'});
                        instance.getPreloader('delete','hide');
                        instance.renderTooltip('');

                    }
                );
                this.getHide('hide',true);
            },
            renderTooltip(t)
            {
                $('.tooltipped').tooltip(t);
            }
        },
        beforeDestroy() {
            window.removeEventListener('resize', this.getWindowWidth);
        },
    }
</script>