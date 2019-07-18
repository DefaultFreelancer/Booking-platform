<template>
    <div class="main-layout-card">
        <div class="main-layout-card-header-with-button">
            <div class="main-layout-card-content-wrapper">
                <div class="main-layout-card-header-contents">
                    <h5 class="bluish-text no-margin">{{ trans('lang.user_list') }}</h5>
                </div>
                <div class="main-layout-card-header-contents right-align">
                    <button class="btn waves-effect waves-green modal-trigger" data-target="user-invite-modal" @click.prevent="isActive=true">{{ trans('lang.add') }}</button>
                </div>
            </div>
        </div>
        <div class="main-layout-card-content">
            <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                <div class="modal-loader-child">
                    <preloaders :loderType="preloaderType"></preloaders>
                </div>
            </div>
            <datatable-component :options="tableOptions" @setPreloader="getPreloader" v-show="hidePreloader=='hide'"></datatable-component>
        </div>
        <div id="user-invite-modal" class="modal modal-layout">
            <invite-user v-if="isActive" @setShowInvite="getShowInvite"></invite-user>
        </div>
        <div id="edit-user-invite-modal" class="modal modal-layout">
            <div v-if="editIsActive">
                <div class="row margin-fix modal-layout-header">
                    <div class="col s10">
                        <h5 class="bluish-text no-margin">{{ trans('lang.change_user_role') }}</h5>
                    </div>
                    <div class="col s2 right-align">
                        <a href="#" class="modal-close" @click.prevent="editIsActive=false"><i class="material-icons dp48 grey-text icon-vertically-middle">clear</i></a>
                    </div>
                </div>
                <div class="modal-loader-parent"  v-if="hidePreloader2!='hide'">
                    <div class="modal-loader-child">
                        <preloaders :loderType="preloaderType2"></preloaders>
                    </div>
                </div>
                <div class="modal-layout-content" v-show="hidePreloader2=='hide'">
                    <div class="row">
                        <div class="col s12 m6">
                            <div class="row">
                                <label>{{ trans('lang.name') }}</label>
                            </div>
                            <span>{{ selectedUserName }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <select v-model="role_id" id="role">
                                <option disabled>{{ trans('lang.choose_one') }}</option>
                                <option :value="role.id" v-for="role in roles">{{ role.title }}</option>
                            </select>
                            <label for="role">{{ trans('lang.role') }}</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12 m8 l8 xl6">
                            <button class="btn waves-effect waves-light bluish-back mob-btn" @click.prevent="is_disable(),save()" :disabled="is_disabled">{{ trans('lang.save') }}</button>
                            <button class="btn cancel-btn waves-effect waves-light mob-btn modal-close" @click.prevent="">{{ trans('lang.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="confirm-delete" class="modal modal-layout confirm-delete-modal">
            <confirmation-modal :message = "'app_deleted_permanently'" :firstButtonName="'yes'" :secondButtonName = "'no'" @confirmationModalButtonAction = "confirmationModalButtonAction"></confirmation-modal>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data(){
            return{
                users:[],
                currentUser:'',
                selectedUserId:'',
                selectedUserName:'',
                isActive:false,
                editIsActive: null,
                hidePreloader:'',
                preloaderType:'load',
                hidePreloader2:'',
                preloaderType2:'load',
                roles:[],
                role_id:0,
                showInvite:false,
                is_disabled:false,
                user:'',
                id:'',
                tableOptions: {
                    tableName: 'users',
                    columns: [
                        {title: 'lang.name', key: 'full_name', type: 'clickable_link', source:'/user', uniquefield:'id', sortable: true},
                        {title: 'lang.emails', key: 'email', type: 'text', sortable: true},
                        {title: 'lang.role', key: 'role_title', type: 'component', componentName:'user-list-role', sortable: true},
                        {title: 'lang.action', key: 'currentUser', type: 'component', componentName:'userlist-action-component',
                            modifier: function(value){
                                if(value) return false;
                                else return true;
                            }
                        }
                    ],
                    source: '/userlist',
                    search: true
                }
            }
        },
        mounted(){
            let instance = this;
            $('select').formSelect();
            $('.dropdown-trigger').dropdown();
            $('#user-invite-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function()
                    {
                        instance.isActive = false;
                        instance.renderTooltip('');
                    }, // Callback for Modal close
                }
            );
            $('#confirm-delete').modal();
            // Listens event from datatable action component
            this.editAction();
            this.changeUserRole();
            this.$hub.$on('renderTooltip', function(value){
                instance.renderTooltip(value);
            });
            $('#edit-user-invite-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function()
                    {
                        instance.isActive = false;
                        instance.renderTooltip('');
                    },onCloseEnd:function () {
                        instance.isActive = false;
                    }
                }
            );
            this.$hub.$on('disableEnableUser', function (id,status) {
                if(status==1)
                {
                    instance.disableOrEnableId = id;
                    instance.disableOrEnableStatus = status;
                }
                else
                {
                    instance.disableEnableUser(id,status);
                }
            });
            // Listens event from datatable action component
            this.editAction();
            this.changeUserRole();
            this.userDetails();
            this.$hub.$on('renderTooltip', function(value){
                instance.renderTooltip(value);
            });
        },
        methods:{
            confirmationModalButtonAction()
            {
                this.disableEnableUser(this.disableOrEnableId,this.disableOrEnableStatus);
            },
            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            setPreLoader: function(type,action){
                this.preloaderType2 = type;
                this.hidePreloader2 = action;
            },
            getShowInvite: function(e){
                this.showInvite = e;
            },
            changeUserRole(){
                let instance = this;
                instance.$hub.$on('changeUserRole', function(id,name,roleId){
                    instance.getRoles();
                    instance.renderSelect();
                    instance.selectedUserId = id;
                    instance.selectedUserName = name;
                    //instance.getPreloader('load','hide');
                    instance.role_id = roleId;
                })
            },
            getRoles(){
                var instance = this
                instance.setPreLoader('load','');
                this.axiosGet('/roletitleuser',
                    function(response){
                        instance.roles = response.data;
                        instance.setPreLoader('load','hide');
                        setTimeout(function () {
                            $("#role").formSelect();
                        })
                    },
                    function (response) {
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreLoader('load','hide');
                    },
                );
            },
            userDetails(){

                let instance = this;
                instance.$hub.$on('userDetails', function(id){
                    instance.redirect("/user/"+id);
                });
            },
            save()
            {
                var instance = this;
                instance.setPreLoader('load','');
                instance.axiosPost('/roleassign/'+instance.selectedUserId,{
                        role_id: instance.role_id,
                    },
                    function(response){
                        M.toast({html:instance.trans('lang.user_successfully_saved')});
                        instance.is_disabled = false;
                        instance.setPreLoader('load','hide');
                        instance.is_disabled = false;
                        $('#edit-user-invite-modal').modal('close');
                    },
                    function (error) {
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreLoader('load','hide');
                        instance.is_disabled = false;
                        $('#edit-user-invite-modal').modal('close');

                    }
                );
            },
            disableEnableUser(id,status)
            {
                var instance = this;
                if(status==0)
                {
                    instance.setPreLoader('load','');
                }
                else {
                    instance.setPreLoader('delete','');
                }
                instance.axiosPost('/disableEnableUser/'+id,{
                        disabled: status,
                    },
                    function(response){
                        if(status==0)
                        {
                            M.toast({html:instance.trans('lang.user_enable_successfully')});
                            instance.$hub.$emit('reloadDataTable');
                            instance.setPreLoader('load','hide');
                        }
                        else
                        {
                            M.toast({html:instance.trans('lang.user_disable_successfully')});
                            instance.$hub.$emit('reloadDataTable');
                            instance.setPreLoader('delete','hide');
                        }
                    },
                    function (error) {
                        M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        instance.setPreLoader('load','hide');

                    }
                );
            },
            is_disable()
            {
                this.is_disabled = true;
            },
            renderSelect(){
                var instance = this;
                $(document).ready(function () {
                    $('select').formSelect();
                    $('#role').on('change', function() {
                        let value = $(this).val();
                        instance.role_id = value;
                    });
                });
            },
            renderTooltip(t)
            {
                $('.tooltipped').tooltip(t);
            },
            editAction(){
                let instance = this;
                instance.$hub.$on('editAction', function(value){
                    instance.editIsActive = value;
                })
            }
        }
    }
</script>
