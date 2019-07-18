<script>
    import { HorizontalBar } from 'vue-chartjs';

    export default {
        props: ['data'],
        data(){
            return{
                options: {responsive: true, maintainAspectRatio: false}
            }
        },
        extends: HorizontalBar,
        watch:{
            'data': function () {
                let instance = this;
                instance.$data._chart.destroy();
                instance.renderChart(instance.data, instance.options)
            }
        },

        mounted() {
            this.renderChart(this.data, this.options)
        },
        methods:{
            
        }
    }
</script>
