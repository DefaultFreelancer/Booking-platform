<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.clients') }}
        </div>
        <div class="admin-layout-margin">
            <div class="main-layout-card">
                <div class="main-layout-card-header">
                    <div class="main-layout-card-content-wrapper">
                        <div class="main-layout-card-header-contents">
                            <h5 class="bluish-text no-margin">{{ trans('lang.clients') }}</h5>
                        </div>
                    </div>
                </div>
                <div class="main-layout-card-content">
                    <div class="row margin-fix">
                        <div class="modal-loader-parent" v-if="hidePreloader!='hide'">
                            <div class="modal-loader-child">
                                <preloaders :loderType="preloaderType"></preloaders>
                            </div>
                        </div>
                        <div class="row margin-fix" v-show="hidePreloader=='hide'">
                            <datatable-component :options="tableOptions"
                                                 @setPreloader="getPreloader"></datatable-component>
                        </div>
                    </div>
                </div>
            </div>
            <div id="client-modal" class="modal modal-layout">
                <client-edit v-if="isActive" @setActive="getActive" :clientId="clientId"
                             :publicPath="publicPath"></client-edit>
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
                users: [],
                clientId: 0,
                isActive: false,
                hidePreloader: '',
                preloaderType: 'load',
                is_disabled: false,
                user: '',
                id: '',
                tableOptions: {
                    tableName: 'clients',
                    columns: [
                        {
                            title: 'lang.name',
                            key: 'full_name',
                            type: 'clickable_link',
                            source: 'client',
                            uniquefield: 'id',
                            sortable: true
                        },
                        {title: 'lang.emails', key: 'email', type: 'text', sortable: true},
                        {title: 'lang.phone', key: 'phone', type: 'text', sortable: true},
                        {title: 'lang.action', type: 'component', componentName: 'client-action-component'}
                    ],
                    source: '/clientlist',
                    search: true,
                }
            }
        },
        mounted() {
            let instance = this;
            $('#client-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function () {
                        instance.isActive = false;
                        instance.renderTooltip('');
                    },onCloseEnd:function () {
                        instance.isActive = false;
                    }
                }
            );
            this.clientSetId()
            $('select').formSelect();
            $('.dropdown-trigger').dropdown();
            // Listens events from datatable action component
            this.$hub.$on('editUser', function (id) {
                instance.editUser(id);
            });
            this.$hub.$on('getActive', function (isActive) {
                instance.getActive(isActive);
            });
            this.$hub.$on('renderTooltip', function (value) {
                instance.renderTooltip(value);
            });
        },
        methods: {
            getActive(isActive) {
                this.isActive = isActive;
            },

            getPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },

            clientSetId() {
                let instance = this;
                instance.$hub.$on('clientSetId', function (id) {
                    instance.clientId = id;
                })
            },
            editUser(id) {
                this.clientId = id;
            },

            userDetails(id) {
                this.axiosGetWithData('/client/' + id, {
                        users: this.user.id
                    },
                    function (response) {
                        instance.$nextTick(function () {
                            M.updateTextFields();
                        });
                    },
                    function (response) {
                    },
                );

                this.redirect("/client/" + id);

            },
            is_disable() {
                this.is_disabled = true;
            },
            renderTooltip(t) {
                $('.tooltipped').tooltip(t);
            }
        }
    }
</script>