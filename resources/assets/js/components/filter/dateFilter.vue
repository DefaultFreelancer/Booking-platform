<template>
    <div class="date-filter-wrapper">
        <p v-if="label" class="date-filter-label">{{ trans(label) }}</p>
        <p v-else class="date-filter-label">{{ trans('lang.date_range') }}</p>
        <div id="date-filter" class="date-range-filter">
            <h6 class="no-margin">
                <div class="row margin-fix">
                    <div class="input-field col s5 margin-top-date-input-field no-padding">
                        <input v-if="!showCustom" type="text" class="datepicker date-range-input" disabled>
                        <input v-show="showCustom" :id="'from-date-'+id" type="text" class="datepicker date-range-input" :value="showFromResult()">
                    </div>
                    <div class="col s1 date-filter-area date-demo" >
                        <p class="date-between center-align date-demo"> - </p>
                    </div>
                    <div class="input-field col s4 margin-top-date-input-field no-padding">
                        <input v-if="!showCustom" type="text" class="datepicker date-range-input" disabled>
                        <input v-show="showCustom" :id="'to-date-'+id" type="text" class="datepicker date-range-input" :disabled="from==0 ? 'disabled': null" :value="showToResult()">
                    </div>
                    <div class="col s1 dateRangeIcon">
                        <a class='date-dropdown-trigger floatRight' href='#' :data-target="'date-dropdown-'+id" @click.prevent="">
                            <i class="material-icons dp48 black-text">arrow_drop_down</i>
                        </a>
                    </div>
                </div>
            </h6>
        </div>
        <!-- Start Dropdown Structure -->
        <ul :id="'date-dropdown-'+id" class='dropdown-content date-range-dropdown'>
                <li><a href="#"  @click.prevent="changeDateDuration(0)":class="{'selected-date-filter':selectedDateFilter==''}">{{ trans('lang.all') +' / '+ trans('lang.customs') }}</a></li>

                <li><a href="#"  @click.prevent="changeDateDuration('today')" :class="{'selected-date-filter':selectedDateFilter=='today'}">{{ trans('lang.today') }}</a></li>

                <li><a href="#"  @click.prevent="changeDateDuration('yesterday')" :class="{'selected-date-filter':selectedDateFilter=='yesterday'}">{{ trans('lang.yesterday') }}</a></li>

                <li><a href="#"  @click.prevent="changeDateDuration('next_7_days')" :class="{'selected-date-filter':selectedDateFilter=='next_7_days'}">{{ trans('lang.next_7_days') }}</a></li>

                <li><a href="#" @click.prevent="changeDateDuration('this_month')" :class="{'selected-date-filter':selectedDateFilter=='this_month'}">{{ trans('lang.this_month') }}</a></li>

                <li><a href="#" @click.prevent="changeDateDuration('this_year')" :class="{'selected-date-filter':selectedDateFilter=='this_year'}">{{ trans('lang.this_year') }}</a></li>

                <!--<li><a href="#" @click.prevent="changeDateDuration('custom')">{{ trans('lang.customs') }}</a></li>-->
            </ul>
        <!-- End Dropdown Structure -->
    </div>
</template>
<script>
    import axiosGetPost from '../../helper/axiosGetPostCommon';
    export default {
        extends: axiosGetPost,
        props: ['label','id'],
        data (){
            return{
                from:0,
                to:0,
                thisMonth:moment().format("MM"),
                thisYear:moment().format("YYYY"),
                firstDay: moment().startOf('month').format('D'),
                lastDay: moment().endOf('month').format('D'),
                showCustom:true,
                selectedDateFilter:'',
                showFromResult()
                {
                    if(this.from==0)
                    {
                        return "";
                    }
                    else
                    {
                        return this.dateformat(this.from[0]+','+this.from[1]+','+this.from[2]);
                    }
                },
                showToResult()
                {
                    if(this.to==0)
                    {
                        return "";
                    }
                    else
                    {
                        return this.dateformat(this.to[0]+'-'+this.to[1]+'-'+this.to[2]);
                    }
                },
            }
        },
        mounted()
         {
            this.renderFromDate();
            let instance =this;
            $('#from-date-'+instance.id).on('change', function () {
                let value = $(this).val();
                instance.from = value.split(',');
                if(instance.from[0]=='')
                {
                    instance.from = 0;
                }
                instance.renderDate(instance.from,1);
               instance.setDatefilterValue(instance.from, 0);
            });
            $('#to-date-'+instance.id).on('change', function () {
                let value = $(this).val();
                instance.to = value.split(',');
                if(instance.to[0]=='')
                {
                    instance.to = 0;
                }
                instance.setDatefilterValue(instance.from, instance.to);
            });
            $('.date-dropdown-trigger').dropdown({
                alignment: 'right',
                gutter: 0,
                belowOrigin:true,
            });
        },
        methods:
        {
            setDatefilterValue: function(fromDate,toDate)
            {
                //pass the values to parent
                this.$emit('setDatefilterValue', fromDate, toDate, this.id);
            },
            dateformat(dateUnformed)
            {
                let instance = this;
                return moment(dateUnformed,'YYYY-MM-DD').format(instance.dateFormat.toUpperCase());
            },
            renderFromDate()
            {
                var instance =this;
                let $input = $('#from-date-'+instance.id).datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    format: 'yyyy,mm,dd',
                    highlight: 'Today',
                    closeOnSelect: true, // Close upon selecting a date,
                    //container: 'body'
                });
            },
            changeDateDuration(value)
            {
                var instance = this;
                if(value=='today')
                {
                    instance.showCustom = true;
                    instance.from = (moment().format("YYYY/MM/DD")).split('/');
                    instance.to = (moment().format("YYYY/MM/DD")).split('/');
                    instance.renderFromDate();
                    instance.renderDate(instance.to,2);
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='today';

                }
                else if(value=='yesterday')
                {
                    instance.showCustom = true;
                    instance.from = (moment().add(-1, 'days').format("YYYY/MM/DD")).split('/');
                    instance.to = (moment().add(-1, 'days').format("YYYY/MM/DD")).split('/');
                    instance.renderFromDate();
                    instance.renderDate(instance.to,2);
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='yesterday';
                }
                else if(value=='next_7_days')
                {
                    instance.showCustom = true;
                    instance.from = (moment().format("YYYY/MM/DD")).split('/');
                    instance.to = (moment().add(6, 'days').format("YYYY/MM/DD")).split('/');
                    instance.renderFromDate();
                    instance.renderDate(instance.to,2);
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='next_&_days';
                }
                else if(value=='this_month')
                {
                    instance.showCustom = true;
                    instance.from = (moment(instance.firstDay+"/"+instance.thisMonth+"/"+instance.thisYear,"DD/MM/YYYY").format("YYYY/MM/DD")).split('/');
                    instance.to = (moment(instance.lastDay+"/"+instance.thisMonth+"/"+instance.thisYear,"DD/MM/YYYY").format("YYYY/MM/DD")).split('/');
                    instance.renderFromDate();
                    instance.renderDate(instance.to,2);
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='this_month';
                }
                else if(value=='this_year')
                {
                    instance.showCustom = true;
                    instance.from = (moment('01'+"/"+'01'+"/"+instance.thisYear,"DD/MM/YYYY").format("YYYY/MM/DD")).split('/');
                    instance.to = (moment('31'+"/"+'12'+"/"+instance.thisYear,"DD/MM/YYYY").format("YYYY/MM/DD")).split('/');
                    instance.renderFromDate();
                    instance.renderDate(instance.to,2);
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='this_year';
                }
                else if(value=='custom')
                {
                    instance.showCustom = true;
                    instance.from = [];
                    instance.to = [];
                    instance.renderFromDate();
                    instance.selectedDateFilter='custom';
                    //instance.renderDate(instance.to,2);
                }
                else
                {
                    instance.showCustom = true;
                    instance.from = 0;
                    instance.to = 0;
                    instance.setDatefilterValue(instance.from, instance.to);
                    instance.selectedDateFilter='';
                    //instance.renderFromDate();
                    //instance.renderDate(instance.to,2);
                }
                //instance.readData();
            },
            renderDate(data,flag)
            {
                let instance = this;
                var $input =  $('#to-date-'+instance.id).datepicker({
                    selectMonths: true, // Creates a dropdown to control month
                    selectYears: 70, // Creates a dropdown of 70 years to control year,
                    today: 'Today',
                    clear: 'Clear',
                    close: 'Ok',
                    format: 'yyyy,mm,dd',
                    highlight: 'Today',
                    closeOnSelect: true, // Close upon selecting a date,
                    //min: [from[0], from[1] - 1, from[2]],
                    //max: [instance.to]
                });
            },
        }
    }
</script>