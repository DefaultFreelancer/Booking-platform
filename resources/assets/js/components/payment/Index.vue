<template>
    <div>
        <div class="main-layout-card">
            <div class="main-layout-card-header-with-button">
                <div class="main-layout-card-content-wrapper">
                    <div class="main-layout-card-header-contents">
                        <h5 class="bluish-text no-margin">{{ trans('lang.payment_methods') }}</h5>
                    </div>
                    <div class="main-layout-card-header-contents right-align">
                        <button class="btn waves-effect waves-light bluish-back modal-trigger" data-target="payment-modal" @click.prevent=" isActive = true , addOrEditPayment('')">{{ trans('lang.add') }}</button>
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
                    <datatable-component :options="tableOptions" @setPreloader="getPreloader" ></datatable-component>
                </div>
            </div>
        </div>
        <div id="payment-modal" class="modal modal-layout">
            <payment-details v-if="isActive" @setActive="getActive" :id="selectedItemId"></payment-details>
        </div>
        <div id="confirm-delete" class="modal modal-layout confirm-delete-modal">
            <confirmation-modal :message = "'payment_method_deleted_permanently'" :firstButtonName="'yes'" :secondButtonName = "'no'" @confirmationModalButtonAction = "confirmationModalButtonAction"></confirmation-modal>
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
                isActive: false,
                items: [],
                selectedItemId: '',
                deleteID: null,
                deleteIndex: null,
                preloaderType:'load',
                hidePreloader:'',
                dateformat:'',
                tableOptions: {
                    tableName: 'payments',
                    columns: [
                        {title: 'lang.name', key: 'title', type: 'text', sortable: true, },
                        {title: 'lang.available_to_client', key: 'available_to_client', type: 'custom', sortable: true,
                            modifier: function(rowData){
                                if(rowData == '1') return {value: instance.trans('lang.yes')};
                                else if(rowData == '0') return {value: instance.trans('lang.no')};
                            }
                        },
                        {title: 'lang.enabled', key: 'enable', type: 'custom', sortable: true,
                            modifier: function(rowData){
                                if(rowData == '1') return {value: instance.trans('lang.yes')};
                                else if(rowData == '0') return {value: instance.trans('lang.no')};
                            }
                        },
                        {title: 'lang.action', type: 'component', componentName: 'payment-action-component'}
                    ],
                    source:'/payments',
                    search: false,
                }
            }
        },
        mounted(){

            let instance = this;
            // Listens events from datatable action component
            this.$hub.$on('addOrEditPayment', function(id){
                instance.addOrEditPayment(id);
            });
            this.$hub.$on('getActive', function(isActive){
                instance.getActive(isActive);
            });
            this.$hub.$on('selectedDeletableId', function(id,index){
                instance.deleteID = id;
                instance.deleteIndex = index;
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
            $('#payment-modal').modal(
                {
                    inDuration: 300, // Transition in duration
                    outDuration: 200, // Transition out duration
                    complete: function()
                    {
                        instance.isActive = false;
                    }, onCloseEnd:function () {
                        instance.isActive = false;
                    }
                }
            );
        },

        methods: {
            confirmationModalButtonAction()
            {
                this.deletePayment();
            },
            getActive(isActive){
                this.isActive = isActive;
            },

            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },

            addOrEditPayment(id)
            {
                this.selectedItemId = id;
            },
            deletePayment() {
                let instance = this;
                if(this.deleteID)
                {
                    this.getPreloader('delete','');

                    this.axiosPost('/payment-delete/' + this.deleteID,{
                        },
                        function(response){
                            M.toast({html:instance.trans('lang.payment_method_deleted_successfully')});
                            instance.$hub.$emit('updateDataAfterDelete',instance.deleteIndex);
                            instance.getPreloader('delete','hide');
                        },
                        function (error) {
                            instance.errors = error.data.errors;
                            instance.getPreloader('delete','hide');
                            M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                        });
                }
            },
        },
    }

</script>