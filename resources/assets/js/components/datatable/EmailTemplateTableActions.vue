<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a href="#" @click.prevent="">
            <i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>
        </a>
        <!--<ul>-->
            <!--<li @click.prevent="seen = false, roleHide ='', viewRole(rowData.id,rowData.title)"  :value="rowData.id">-->
                <!--<a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped"  href="#roles-modal"-->
                   <!--data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')" @click.prevent="renderTooltip('remove')">-->
                    <!--<i class="material-icons white-text" >mode_edit</i>-->
                <!--</a>-->
            <!--</li>-->
        <!--</ul>-->
        <ul>
            <li @click.prevent="viewTemplate(rowData.id,rowData.template_type)">
                <a class="btn-floating bluish waves-effect waves-light tooltipped modal-trigger" href="#email-template-modal"
                   data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')">
                    <i class="material-icons white-text icon-vertically-middle" >mode_edit</i>
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
            viewTemplate(id,title){
                this.$hub.$emit('viewTemplateEdit', id, title);
            },
        }
    }
</script>
