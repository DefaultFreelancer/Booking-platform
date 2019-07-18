<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a href="#" @click.prevent="">
            <i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>
        </a>
        <ul>
            <li @click.prevent="disableEnableUser(rowData.id,1)" v-if="rowData.disabled==0 && rowData.is_admin==0">
                <a class="btn-floating waves-effect materialize-red modal-trigger tooltipped" href="#confirm-delete"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.disable')">
                    <i class="la la-minus-circle"></i>
                </a>
            </li>
            <li @click.prevent="disableEnableUser(rowData.id,0)" v-if="rowData.disabled==1  && rowData.is_admin==0">
                <a class="btn-floating waves-effect green tooltipped" href="#"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.enable')">
                    <i class="la la-check"></i>
                </a>
            </li>
            <li @click.prevent="editAction(), changeUserRole(rowData.id, rowData.full_name, rowData.role_id)">
                <a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped" href="#edit-user-invite-modal" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')">
                    <i class="material-icons white-text" >mode_edit</i>
                </a>
            </li>
            <li @click.prevent="userDetails(rowData.id)"><a class="btn-floating bluish waves-effect waves-light tooltipped" href="#" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.view')" ><i class="la la-caret-square-o-right"></i></a></li>
        </ul>
    </div>
</template>


<script>
export default {
    props: ['rowData'],
    data(){
        return{
        }
    },
    mounted(){
        $('.fixed-action-btn').floatingActionButton({  direction: 'left',});
    },
    methods:{
        editAction(){
            this.$hub.$emit('editAction',true);
        },
        changeUserRole(id,name,roleId){
            this.$hub.$emit('changeUserRole',id,name,roleId);
        },
        disableEnableUser(id,status){
            this.$hub.$emit('disableEnableUser', id,status);
        },
        userDetails(id){
            this.$hub.$emit('userDetails', id);
        }
    }
}
</script>
