<template>
    <div>
        <div class="main-layout-card">
            <div class="main-layout-card-header-with-button">
                <div class="main-layout-card-content-wrapper">
                    <div class="main-layout-card-header-contents">
                        <h5 class="bluish-text no-margin">{{ trans('lang.holidays') }}</h5>
                    </div>
                    <div class="main-layout-card-header-contents right-align">
                        <button class="btn waves-effect waves-light bluish-back modal-trigger" data-target="holiday-modal" @click.prevent="getActive(), triggerHolidayModal(), addOrEditHoliday('')">{{ trans('lang.add') }}</button>
                    </div>
                </div>
            </div>
            <div class="main-layout-card-content">
                <div class="modal-loader-parent"  v-if="hidePreloader!='hide'">
                    <div class="modal-loader-child">
                        <preloaders :loderType="preloaderType" ></preloaders>
                    </div>
                </div>
                <calendar :id="selectedItemId" :dateformat="dateformat" v-show="hidePreloader=='hide'" @setPreloader="getPreloader"></calendar>

            </div>
        </div>
        <div id="confirm-holiday-delete" class="modal modal-layout confirm-delete-modal">
            <confirmation-modal :message = "'holiday_will_be_deleted'" :firstButtonName="'yes'" :secondButtonName = "'no'" @confirmationModalButtonAction = "confirmationModalButtonAction"></confirmation-modal>
        </div>
    </div>
</template>

<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        data() {
            return {
                items: [],
                selectedItemId: '',
                deleteID: null,
                deleteIndex: null,
                preloaderType:'load',
                hidePreloader:'',
                dateformat:'',
                tableOptions: {
                    tableName: 'holiday',
                    columns: [
                        {title: 'lang.title', key: 'title', type: 'text', sortable: true, },
                        {title: 'lang.start_date', key: 'start_date', type: 'text', sortable: true},
                        {title: 'lang.end_date', key: 'end_date', type: 'text', sortable: true},
                        {title: 'lang.action', type: 'component', componentName: 'holiday-action-component'}
                    ],
                    source:'/getholidays',
                    search: false,
                }
            }
        },
        mounted(){
            let instance = this;
            // Listens events from datatable action component
            this.$hub.$on('addOrEditHoliday', function(id){
                instance.addOrEditHoliday(id);
            });
            this.$hub.$on('getDeleteId', function(id){
                instance.getDeleteId(id);
            });
        },

        methods: {
            confirmationModalButtonAction()
            {
                this.deleteHoliday();
            },
            triggerHolidayModal(){
                this.$hub.$emit('triggerHolidayModal',undefined);
            },
            getOffDays(){
                this.$hub.$emit('getOffDays');
            },
            getHolidays(){
                this.$hub.$emit('getHolidays');
            },
            getDeleteId(id)
            {
                this.deleteID = id;
            },
            getActive(){
                this.$hub.$emit('getActive',true);
            },
            getPreloader: function(type,action){
                this.preloaderType = type;
                this.hidePreloader = action;
            },
            addOrEditHoliday(id)
            {
                this.selectedItemId = id;
            },
            deleteHoliday() {
                let instance = this;
                $('#holiday-modal').modal('close');
                if(this.deleteID)
                {
                    this.getPreloader('delete','');

                    this.axiosPost('/delete/' + this.deleteID,{

                        },
                        function(response){
                            M.toast({html:instance.trans('lang.holiday_deleted_successfully')});
                            instance.getPreloader('delete','hide');
                            instance.getOffDays();
                            instance.getHolidays();
                        },
                        function (error) {
                            instance.errors = error.data.errors;
                            instance.getPreloader('delete','hide');
                            M.toast({html:instance.trans('lang.getting_problems'),classes:'red lighten-1'});
                            instance.getOffDays();
                            instance.getHolidays();
                        });
                }
            },
        },
    }

</script>