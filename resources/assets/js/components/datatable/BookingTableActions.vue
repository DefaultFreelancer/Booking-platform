<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a href="#" @click.prevent=""><i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i></a>
        <ul>
            <li v-if="rowData.status === 'pending' && rowData.payment_stat !==0" @click.prevent="cancelBookingFromDatatable(rowData.id)" ><a href="#confirm-cancel-booking" class="btn-floating waves-effect red tooltipped modal-trigger" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.cancel')"><i class="material-icons white-text icon-vertically-middle">clear</i></a></li>
            <li v-if="rowData.status === 'pending'" @click.prevent="confirm(rowData.id)"><a href="#" class="btn-floating waves-effect green tooltipped" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.confirm')"><i class="material-icons white-text icon-vertically-middle">check</i></a></li>

            <li v-if="rowData.payment_stat > 0 && rowData.status != 'canceled'" @click.prevent="duePayment(rowData.id),getActive(2)">
                <a href="#due-payment-modal" class="modal-trigger btn-floating bluish waves-effect waves-light cyan lighten-1 tooltipped"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.pay')"><i class="la la-money"></i></a></li>

            <li @click.prevent="bookingDetails(rowData.id)"><a href="#" class="btn-floating bluish waves-effect waves-light tooltipped" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.view')"><i class="la la-caret-square-o-right"></i></a></li>
        </ul>
    </div>
</template>


<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
export default {
    extends: axiosGetPost,
    props: ['rowData'],
    data(){
        return{

        }
    },
    mounted(){
        $('.fixed-action-btn').floatingActionButton({  direction: 'left',});
    },
    methods:{

        readData(){
            this.$emit('readData');
        },
        getActive(isActive){
            this.$hub.$emit("getActive", isActive);
        },
        setPreloader(type,action){
            this.$emit('setPreloader',type,action);
        },
        confirm(id){
            let instance = this;
            instance.setPreloader('load','');
            instance.axiosPost('/actionbooking/'+id,{
                    status: 'confirmed'
                },
                function(response){
                    M.toast({html:instance.trans('lang.booking_is_confirmed')});
                    instance.readData();
                },
                function (error) {
                    instance.errors = error.data.errors
                    instance.setPreloader('load','hide');
                }
            );
        },
        bookingDetails(id){
            this.$hub.$emit('bookingDetails', id)
        },
        cancelBookingFromDatatable(id){
            this.$hub.$emit('cancelBookingFromDatatable', id)
        },
        duePayment(id){
            this.$hub.$emit('duePayment', id)
        },
        changeBookingValue(value){
            this.bookingWatcher = value;
        }
    }
}
</script>
