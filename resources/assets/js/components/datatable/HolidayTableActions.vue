<!--<template>-->
    <!--<div>-->
        <!--<div class="fixed-action-btn horizontal action-com-button">-->
            <!--<a href="#" @click.prevent="">-->
                <!--<i class="material-icons purple-grey-text text-darken-1 icon-vertically-middle">more_vert</i>-->
            <!--</a>-->
            <!--<ul>-->
                <!--<li @click.prevent="selectedDeletableId(rowData.id,rowIndex)"><a class="btn-floating waves-effect materialize-red modal-trigger tooltipped" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.delete')" href="#confirm-delete" @click.prevent="renderTooltip('remove')"><i class="material-icons white-text"  >delete_forever</i></a></li>-->
                <!--<li @click.prevent="getActive(),addOrEditHoliday(rowData.id)"><a class="btn-floating bluish waves-effect waves-light modal-trigger tooltipped" href="#holiday-modal" data-position="bottom" data-delay="50" :data-tooltip="trans('lang.edit')" @click.prevent="renderTooltip('remove')"><i class="material-icons white-text" >mode_edit</i></a></li>-->
            <!--</ul>-->
        <!--</div>-->
    <!--</div>-->
<!--</template>-->


<!--<script>-->
<!--export default {-->
    <!--props: ['rowData','rowIndex'],-->
    <!--data(){-->
        <!--return{-->

        <!--}-->
    <!--},-->
    <!--mounted(){-->
        <!--this.renderTooltip('');-->
    <!--},-->
    <!--methods:{-->
        <!--selectedDeletableId(id,index)-->
        <!--{-->
          <!--this.$hub.$emit('selectedDeletableId',id,index);-->
        <!--},-->
        <!--addOrEditHoliday(id){-->
            <!--this.$hub.$emit('addOrEditHoliday',id);-->
        <!--},-->
        <!--getActive(){-->
            <!--this.$hub.$emit('getActive',true);-->
        <!--},-->
        <!--renderTooltip(action){-->
            <!--this.$hub.$emit('renderTooltip',action);-->
        <!--},-->
        <!--setPreloader(type,activity){-->
            <!--this.$emit('setPreloader',type,activity);-->
        <!--},-->
        <!--readData(){-->
            <!--this.$emit('readData');-->
        <!--},-->
        <!--getHideHoliday(e,f){-->
            <!--this.$hub.$emit('getHideHoliday',e,f);-->
        <!--},-->
    <!--}-->
<!--}-->
<!--</script>-->
