<script>
    import { Doughnut }  from 'vue-chartjs';

    export default {
        extends: Doughnut,
        props:['confirmed','pending'],
        data(){
            return{
            }
        },
        mounted() {
            this.renderDoughnutChart(this.confirmed,this.pending);
        },
        methods:{
            renderDoughnutChart(confirmed,pending){
                this.renderChart({
                        datasets: [{
                            data: [confirmed,pending],
                            backgroundColor: [
                                'rgba(114, 168, 80,1)',
                                'rgba(255, 181, 75,1)',
                            ],
                        }],
                        // These labels appear in the legend and in the tooltips when hovering different arcs
                        labels: [this.trans('lang.confirmed_'),this.trans('lang.pending')],
                    },
                    {responsive: true, maintainAspectRatio: false, cutoutPercentage: 1}
                )
            },
        },
    }
</script>
