<template>
    <div>
        <div class="navbar-text hide-on-med-and-down">
            {{ trans('lang.services') }}
        </div>
        <div class="admin-layout-margin">
            <div class="main-layout-card">
                <div class="main-layout-card-header">
                    <div class="main-layout-card-content-wrapper">
                        <div class="main-layout-card-header-contents">
                            <h5 class="bluish-text no-margin">{{ trans('lang.services') }}</h5>
                        </div>
                        <div class="main-layout-card-header-contents right-align">
                            <button class="btn waves-effect waves-light bluish-back modal-trigger"
                                    data-target="service-modal" @click=" isActive = 2, serviceId = 0">{{
                                trans('lang.add') }}
                            </button>
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
                        <datatable-component ref="dataTable" :options="tableOptions" @setPreloader="getPreloader"
                                             v-show="hidePreloader=='hide'"></datatable-component>
                    </div>
                </div>
            </div>
            <div id="service-modal" class="modal modal-layout">
                <createservice-form v-if="isActive == 2" v-on:setActive="getActive"
                                    :serviceId="this.serviceId"></createservice-form>
                <service-setting v-if="isActive == 4" @setActive="getActive"
                                 :serviceId="this.serviceId"></service-setting>
            </div>
            <div id="confirm-delete" class="modal modal-layout confirm-delete-modal">
                <confirmation-modal :message="'service_deleted_permanently'" :firstButtonName="'yes'"
                                    :secondButtonName="'no'"
                                    @confirmationModalButtonAction="confirmationModalButtonAction"></confirmation-modal>
            </div>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';

    export default {
        extends: axiosGetPost,

        data() {
            let instance = this;
            return {
                isActive: 1,
                serviceId: 0,
                items: [],
                hidePreloader: '',
                preloaderType: 'load',
                tableOptions: {
                    tableName: 'service',
                    columns: [
                        {title: 'lang.title', key: 'title', type: 'text', sortable: true},
                        {title: 'lang.price', key: 'price', type: 'text', sortable: true},
                        {title: 'lang.duration', key: 'service_duration', type: 'text', sortable: true},
                        {title: 'lang.description', key: 'description', type: 'text', sortable: true},
                        {title: 'lang.starts', key: 'service_starts', type: 'text', sortable: true},
                        {title: 'lang.ends', key: 'service_ends', type: 'text', sortable: true},
                        {
                            title: 'lang.status', key: 'activation', type: 'custom', sortable: true,
                            modifier: function (rowData) {
                                if (rowData == '1') return {
                                    value: instance.trans('lang.active'),
                                    class: "booking-status green"
                                };
                                else if (rowData == '0') return {
                                    value: instance.trans('lang.inactive'),
                                    class: "booking-status grey"
                                };
                            }
                        },
                        {title: 'lang.link', key: '', type: 'component', componentName: 'service-copy-component'},
                        {title: 'lang.action', key: '', type: 'component', componentName: 'service-action-component'},
                    ],
                    source: '/serviceshow',
                    search: false,
                },
                deleteID: '',
                deleteIndex: '',
            }
        },

        mounted() {
            let instance = this;
            $('#service-modal').modal({
                inDuration: 300, // Transition in duration
                outDuration: 200, // Transition out duration
                complete: function () {
                    instance.renderTooltip('');
                    instance.isActive = 1;
                }
            });
            $('#confirm-delete').modal({
                    dismissible: true, // Modal can be dismissed by clicking outside of the modal
                    opacity: .5, // Opacity of modal background
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    startingTop: '0', // Starting top style attribute
                    endingTop: '0', // Ending top style attribute
                }
            );
            // Listens events from datatable action component
            this.setActiveService();
            this.serviceSetId();
            this.$hub.$on('renderTooltip', function (value) {
                instance.renderTooltip(value);
            });
            this.$hub.$on('selectedDeletableId', function (id, index) {
                instance.deleteID = id;
                instance.deleteIndex = index;
            })
        },
        methods: {
            confirmationModalButtonAction() {
                this.deleteService(this.deleteID);
            },
            getActive: function (newActive, activity) {
                let instance = this;
                instance.isActive = 0;
                if (activity == 'save') {
                    this.getPreloader('load', '');
                    $('#service-modal').modal('close');
                    instance.isActive = newActive;
                    this.$hub.$emit('reloadDataTable')
                }
                else {
                    this.isActive = newActive;
                }
            },
            setActiveService() {
                let instance = this;
                instance.$hub.$on('setActiveService', function (newActive, action) {
                    $('#service-modal').modal(
                        {
                            inDuration: 300, // Transition in duration
                            outDuration: 200, // Transition out duration
                            complete: function () {
                                instance.isActive = 1;
                                instance.renderTooltip('');
                            },onCloseEnd:function () {
                                instance.isActive = 1;
                            }
                        }
                    );
                    instance.getActive(newActive, action);
                })
            },
            serviceSetId() {
                let instance = this;
                this.$nextTick(function () {
                    instance.$hub.$on('serviceSetId', function (id) {
                        instance.serviceId = id;
                    })
                });
            },
            getPreloader: function (type, action) {
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            renderTooltip(t) {
                $('.tooltipped').tooltip(t);
            },
            deleteService(id) {
                let instance = this;
                this.getPreloader('delete', '');
                instance.axiosPost('/deleteservice/' + id, {},
                    function (response) {
                        instance.$hub.$emit('updateDataAfterDelete', instance.deleteIndex)
                        instance.getPreloader('delete', 'hide');
                        M.toast({html: instance.trans('lang.deleted_successfully')});
                    },
                    function (error) {
                        M.toast({html: instance.trans('lang.getting_problems'), classes: 'red lighten-1'});
                        instance.getPreloader('delete', 'hide');

                    }
                );
            },
        },
    }
</script>