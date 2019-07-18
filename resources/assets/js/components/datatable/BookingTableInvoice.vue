<template>
    <div class="fixed-action-btn horizontal action-com-button">
        <a :href="publicPath+'/inovoice-pdf/' + rowData.id" target="_blank" class="tooltipped" data-position="bottom" :data-tooltip="trans('lang.booking_invoice')">
            <i class="la la-file-pdf-o purple-grey-text text-darken-1 icon-vertically-middle"></i>
        </a>
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
            $(document).ready(function () {
                $('.tooltipped').tooltip();
            });
        },
        methods:{

        }
    }
</script>
