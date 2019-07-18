<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a href="#" @click.prevent="">
            <i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>
        </a>
        <ul>
            <li @click.prevent="changeRoleDeleteId(rowData.id,rowIndex)">
                <a class="btn-floating waves-effect materialize-red modal-trigger tooltipped" href="#confirm-delete"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.delete')">
                    <i class="material-icons white-text">delete_forever</i>
                </a>
            </li>
            <li @click.prevent="seen = false, roleHide ='', viewRole(rowData.id,rowData.title)"  :value="rowData.id">
                <a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped"  href="#roles-modal"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')">
                    <i class="material-icons white-text" >mode_edit</i>
                </a>
            </li>
        </ul>
    </div>
</template>


<script>
    export default {
        props: ['rowData','rowIndex'],
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
            viewRole(id,title){
                this.$hub.$emit('viewRoleEditData', id, title);
            },
            changeRoleDeleteId(id,index){
                this.$hub.$emit('changeRoleDeleteId',id,index);
            },
        }
    }
</script>
