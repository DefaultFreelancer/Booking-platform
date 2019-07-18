<template>
    <div>
        <div class="fixed-action-btn horizontal action-com-button">
            <a href="#" @click.prevent="">
                <i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>
            </a>
            <ul>
                <li v-if="rowData.type != 'stripe' && rowData.type != 'paypal'" @click.prevent="selectedDeletableId(rowData.id,rowIndex)"><a class="btn-floating waves-effect materialize-red modal-trigger tooltipped" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.delete')" href="#confirm-delete"><i class="material-icons white-text"  >delete_forever</i></a></li>
                <li @click.prevent="getActive(),addOrEditPayment(rowData.id)"><a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped" href="#payment-modal" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')"><i class="material-icons white-text" >mode_edit</i></a></li>
            </ul>
        </div>
    </div>
</template>


<script>
    export default {
        props: ['rowData','rowIndex'],
        data(){
            return{

            }
        },
        mounted(){
            $('.fixed-action-btn').floatingActionButton({  direction: 'left',});
        },
        methods:{
            selectedDeletableId(id,index)
            {
                this.$hub.$emit('selectedDeletableId',id,index);
            },
            addOrEditPayment(id){
                this.$hub.$emit('addOrEditPayment',id);
            },
            getActive(){
                this.$hub.$emit('getActive',true);
            },
            setPreloader(type,activity){
                this.$emit('setPreloader',type,activity);
            },
            readData(){
                this.$emit('readData');
            },
            getHidePayment(e,f){
                this.$hub.$emit('getHidePayment',e,f);
            },
        }
    }
</script>
