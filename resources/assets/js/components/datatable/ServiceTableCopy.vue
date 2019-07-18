<template>
    <div>
        <div class="fixed-action-btn horizontal action-com-button">
            <a href="#" class="tooltipped" data-position="bottom" :data-tooltip="trans('lang.click_link')"
               @click.prevent="copyLink(rowData.alias,rowIndex)">
                <i class="la la-copy purple-grey-text text-darken-1 icon-vertically-middle"></i>
            </a>
        </div>
    </div>
</template>
<script>
    export default {
        props: ['rowData', 'rowIndex'],
        data() {
            return {}
        },
        mounted() {

            $(document).ready(function () {
                $('.tooltipped').tooltip();
            });
        },
        methods: {
            copyLink(alias, index) {
                alias = this.publicPath + '/services/' + alias;
                let $temp = $("<input>");
                $("body").append($temp);
                $temp.val(alias).select();
                document.execCommand("copy");
                $temp.remove();
            }

        }
    }
</script>
