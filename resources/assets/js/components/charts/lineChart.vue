<script>
    import { Line } from 'vue-chartjs';

    export default {
        extends: Line,
        props:['linedata'],
        data(){
            return{
            }
        },
        mounted() {
            this.renderLineChart( this.linedata.monthlyArray);
        },
        methods:{
            renderLineChart(lineArray){
                var instance = this;
                this.renderChart({
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July','August','September','October','November','December'],
                    datasets: [
                        {
                            label: instance.trans('lang.bookings'),
                            backgroundColor: 'rgba(38, 199, 219, 1)',
                            data: lineArray
                        }
                    ]
                }, {responsive: true, maintainAspectRatio: false})
            },
        }
    }
</script>
