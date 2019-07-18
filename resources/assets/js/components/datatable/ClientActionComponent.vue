<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a href="#" @click.prevent="">
            <i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>
        </a>
        <ul>
            <li @click.prevent="getActive(),editUser(rowData.id)"><a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped" href="#client-modal" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')"><i class="material-icons white-text icon-vertically-middle" >mode_edit</i></a></li>
        </ul>
    </div>
</template>


<script>
export default {
    props: ['rowData', 'rowIndex'],
    data(){
        return{

        }
    },
    computed: {

    },
    mounted(){

        $('.fixed-action-btn').floatingActionButton({  direction: 'left',});
    },
    methods:{
        getActive(){
            this.$hub.$emit('getActive',true);
        },
        editUser(id){
            this.$hub.$emit("editUser",id);
        },

    }
}
</script>
